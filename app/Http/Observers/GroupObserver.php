<?php

namespace App\Observers;

use App\Group;
use App\GroupHistory;
use Illuminate\Support\Facades\Log;
/**
 * Yersultan
*/
class GroupObserver
{
    public function updating(Group $group)
    {
        $oldGroup = GroupHistory::find($group->id);

        if($oldGroup){
            if($oldGroup->in_archive !== $group->in_archive){ //some shit with archivator
                GroupHistory::create([
                    'action' => $group->in_archive?"archived":"unarchived",
                    'filed_name' =>"in_archive",
                    'group_id' => $group->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
        }
    }
    public function created(Group $group)
    {
        GroupHistory::create([
            'action' => 'created',
            'new_value' => $group->id,
            'filed_name' =>"id",
            'group_id' => $group->id,
            "actor_user" =>request()->user()->id
        ]);
    }
}