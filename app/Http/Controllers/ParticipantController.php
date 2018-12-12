<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Participant;
use App\User;
use App\Role;
use App\BusinessTrainer;
use App\ParticipantHistory; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParticipantController extends Controller
{
    public function items(Request $request)
    {
        $participants =  Participant::filter(Input::all())->with([
            'user.home.district.region'
        ])->orderBy('id', 'desc');
        /**Daulet
         * координатор получает только сущности с его локалити.
         * 
         */
        if($request->user()->hasRole('coordinator')){
            $user_region_id = $request->user()->home->district->region->id;
            $participants->whereHas('user', function($query1) use ($user_region_id) {
                $query1->whereHas('home', function($query) use ($user_region_id) {
                    $query->whereHas('district', function($q) use ($user_region_id) {
                        $q->where('region_id',$user_region_id);
                    });
                });
            });
        }
        if($request->user()->hasRole('business-trainer')){
            $trainer = BusinessTrainer::where('user_id', $request->user()->id)->with([
                'groups'
            ])->first();
            $user_ids = $trainer->groups->pluck('id');
            $participants->whereIn('user_id', $user_ids);
        }
        return $participants->paginate(20);
    }
    public function item($id)
    {
        return Participant::where('id', $id)
            ->with([
                'user.home.district.region',"archive_reason","user.localities","groups.lessons.pages.materials"
            ])->first();
    }
    public function archive($id,Request $request)
    {
        $user = Participant::where("user_id",$id)->first();
        if(!$user->in_archive){
            $this->validate($request, [
                'archive_reason_id' => 'required',
            ]);
            $user->archive_reason_id = $request->get("archive_reason_id");
            User::where("id",$user->user_id)->update([
                "deleted_at"=>\Carbon\Carbon::now()
            ]);
        }else{
            User::where("id",$user->user_id)->update([
                "deleted_at"=>NULL
            ]);
            $user->archive_reason_id = NULL;
        }
        $user->in_archive=$user->in_archive? False:True;
        $user->save();
        User::where("id",$user->user_id)->update([
            "deleted_at"=>\Carbon\Carbon::now()
        ]);
    }
    public function fullDelete($id)
    {
        $user = Participant::find($id);
        $user->user()->delete();
        Participant::destroy($id);
    }
    public function getHistories($id){
        $responce = ParticipantHistory::where("participant_id",$id)->get();
        return response()->json(['status'=>'success','data'=>$responce],200);
    }
    public function saveField($id,Request $request){
        $user_id=$request->get("user_id");
        $key = $request->get("key");
        $value = $request->get("value");
        if($user_id){
            $user = User::find($user_id);
            $user->$key=$value;
            $user->save();
        }
        else{
            $user = Participant::find($id);
            $user->$key=$value;
            $user->save();
        }

    }
    public function saveDocument($id,request $request)
    {
        $this->validate($request, [
            'file' => 'image64:jpeg,jpg,png',
        ]);
        $user = Participant::find($id);
        $type=$request->get('type');
        $file=$request->file('file');
        $old = $user->$type;
        if($file && $type){
            $user->$type = $file;
            $user->save();
        }
    }
    public function removeDocument($id,request $request)
    {
        $user = Participant::find($id);
        $type=$request->get('type');
        $old = $user->$type;
        if($type){
            $user->$type = NULL;
            $user->save();
        }
    }
    public function save(Request $request)
    {
        $this->validate($request, [
            'new_first_name' => 'required|string|max:255',
            'new_last_name' => 'required|string|max:255',
            'new_patronymic' => 'required|string|max:255',
            'new_gender' => 'required',
            'new_disability' => 'required|integer',
            'new_phone' => 'required|string',
            'new_iin' =>'required|integer',
            'new_photo' => 'string',
            'new_email' => 'required|string',
            'locality_id'=>'integer',
        ]);
        //Создание или обновления пользователя
        $newUser =$request->get("user_id") ? User::find($request->get("user_id")):new User;
        $newUser->first_name = $request->get("new_first_name");
        $newUser->last_name=$request->get("new_last_name");
        $newUser->patronymic= $request->get("new_patronymic");
        $newUser->gender=$request->get("new_gender");
        $newUser->iin=$request->get("new_iin");
        $newUser->phone=$request->get("new_phone");
        $newUser->email=$request->get("new_email");
        // $newUser->locality_id=$request->get('locality_id');
        $newUser->role_id=Role::where("name","Participant")->first()->id;
        $newUser->save();
        $newParticipant = $request->get('id')?Participant::find($request->get("id")):new Participant;
        $newParticipant->disability = $request->get("new_disability");
        $newParticipant->user_id=$newUser->id;
        $newParticipant->save();
        //создание или обновлени

    }
    public function getArchiveReasonsList(){
        $role_id = Role::where("name","Participant")->first();
        $responce = \App\ArchiveReasons::where("role_id",$role_id)->get();
        return response()->json(['status'=>'success','data'=>$responce],200);
    }
    public function exportAll(request $request) {

        // styles
        $style_default = [
            'font' => [
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];
        $style_heading = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'd5a6db']
            ]
        ];
        $style_left = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ],
        ];
        $style_center = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        ];
        $style_right = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            ],
        ];

        // data

        $participants_query = Participant::with([
             'user',  
             'archive_reason',
             'user.home.district.region' 
        ]);
        if($request->user()->hasRole('coordinator')){
            $user_region_id = $request->user()->home->district->region->id;
            $participants_query->whereHas('user', function($query1) use ($user_region_id) {
                $query1->whereHas('home', function($query) use ($user_region_id) {
                    $query->whereHas('district', function($q) use ($user_region_id) {
                        $q->where('region_id',$user_region_id);
                    });
                });
            });
        } 
        if($request->user()->hasRole('business-trainer')){
            $trainer = BusinessTrainer::where('user_id', $request->user()->id)->with([
                'groups'
            ])->first();
            $user_ids = $trainer->groups->pluck('id');
            $participants_query->whereIn('user_id', $user_ids);
        }
        $participants = $participants_query->get();
        // creating spreadsheet

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->applyFromArray($style_default);
        $sheet = $spreadsheet->getActiveSheet();

        // writing data

        $sheet->setShowGridlines(false);

        $sheet->setCellValue('A1','№');
        $sheet->getStyle('A1')->applyFromArray($style_heading);
        $sheet->setCellValue('B1','ФИО участника');
        $sheet->getStyle('B1')->applyFromArray($style_heading);
        $sheet->setCellValue('C1','ИИН');
        $sheet->getStyle('C1')->applyFromArray($style_heading);
        $sheet->setCellValue('D1','Пол');
        $sheet->getStyle('D1')->applyFromArray($style_heading);
        $sheet->setCellValue('E1','Дата Рождения');
        $sheet->getStyle('E1')->applyFromArray($style_heading);
        $sheet->setCellValue('F1','Мобильный Телефон');
        $sheet->getStyle('F1')->applyFromArray($style_heading);
        $sheet->setCellValue('G1','Электронная почта');
        $sheet->getStyle('G1')->applyFromArray($style_heading);
        $sheet->setCellValue('H1','Инвалидность');
        $sheet->getStyle('H1')->applyFromArray($style_heading);
        $sheet->setCellValue('I1','Место проживания');
        $sheet->getStyle('I1')->applyFromArray($style_heading);
        $sheet->setCellValue('J1','Архивирован');
        $sheet->getStyle('J1')->applyFromArray($style_heading);
        $sheet->setCellValue('K1','Причина Архивации'); 
        $sheet->getStyle('K1')->applyFromArray($style_heading);

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(43);
        $sheet->getColumnDimension('C')->setWidth(43);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(35);  
        $sheet->getRowDimension('1')->setRowHeight(77);

        // data
        $row = 2;
        $i = 2;
        foreach ($participants as $participant) {
            $col = 'A';
            $sheet->setCellValue($col.$i,$i-1);
            $sheet->getStyle($col.$i)->applyFromArray($style_right);

            $col = 'B';
            $sheet->setCellValue($col.$i,$participant->user->first_name.' '.$participant->user->last_name.' '.$participant->user->patronymic);
            $sheet->getStyle($col.$i)->applyFromArray($style_left);

            $col = 'C';
            $sheet->setCellValue($col.$i,$participant->user->iin);
            $sheet->getStyle($col.$i)->applyFromArray($style_left);
            $sheet->getStyle($col.$i)->getNumberFormat()->setFormatCode('000000000000');

            $col = 'D';
            $sheet->setCellValue($col.$i,$participant->user->gender == 'M' ? 'мужчина' : 'женщина');
            $sheet->getStyle($col.$i)->applyFromArray($style_left);

            /*Daulet
            * TODO: create accessor for date of birth of user
            * пока здесь иин выводиться
            */
            $col = 'E';
            //$sheet->setCellValue($col.$i,$participant->user->iin);
            $user = $participant->user;
            $sheet->setCellValue($col.$i,$user->dateOfBirth());
            $sheet->getStyle($col.$i)->applyFromArray($style_left);

            $col = 'F';
            $sheet->setCellValue($col.$i,$participant->user->phone);
            $sheet->getStyle($col.$i)->applyFromArray($style_right);
            

            $col = 'G';
            $sheet->setCellValue($col.$i,$participant->user->email);
            $sheet->getStyle($col.$i)->applyFromArray($style_right);

            $col = 'H';
            $sheet->setCellValue($col.$i,$participant->disability == 0 ? 'нет инвалидности' : $participant->disability.' степень инвалидности');
            $sheet->getStyle($col.$i)->applyFromArray($style_center);

            $col = 'I';
            $sheet->setCellValue($col.$i,$participant->user->home->name.' '.$participant->user->home->district->name.' '.$participant->user->home->district->region->name);
            $sheet->getStyle($col.$i)->applyFromArray($style_right);

            $col = 'J';
            $sheet->setCellValue($col.$i,$participant->in_archive == 0 ? 'нет' : 'да');
            $sheet->getStyle($col.$i)->applyFromArray($style_right);

            $col = 'K';
            $sheet->setCellValue($col.$i,$participant->in_archive == 0 ? 'нет': $participant->archive_reason->reason);
            $sheet->getStyle($col.$i)->applyFromArray($style_left);  
            $i++;
        }
        // saving
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $hash = md5(time() . rand(1111,9999));
        $writer->save('sheets/'.$hash.'.xlsx');
        $url = public_path('sheets/'.$hash.'.xlsx');
        return url('/').'/sheets/'.$hash.'.xlsx'; 
    }

}
