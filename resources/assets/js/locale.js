/*
*
* import jsons
*
* export constant with module prefix and entitled name of locale, e.g. 
* module group and 2 translations kz, ru -> groupKz, groupRu
* and then import them into app.js
*
* each export should be prefixed and postfixed with comments 
* that start with BEGIN and END respectively
* 
*/

import dKz from './views/dashboard/lang/kz.json';
import dRu from './views/dashboard/lang/ru.json';
import componentsKz from './components/lang/kz.json';
import componentsRu from './components/lang/ru.json';
import btrainerRu from './views/trainers/lang/ru.json';
import btrainerKz from './views/trainers/lang/kz.json';



// BEGIN views/Dashboard
export const dashboardKz = JSON.parse(JSON.stringify(dKz));
export const dashboardRu = JSON.parse(JSON.stringify(dRu));
// END views/Dashboard

// BEGIN Components
let cmpntKz = JSON.parse(JSON.stringify(componentsKz));
export const sidebarKz = cmpntKz.sidebar;
export const profileKz = cmpntKz.profile;


let cmpntRu = JSON.parse(JSON.stringify(componentsRu));
export const sidebarRu = cmpntRu.sidebar;
export const profileRu = cmpntRu.profile;
// END Components
//Participants
import participantsJSONKz from './views/projects/business_advisor/business_school/participants/lang/kz.json';
import participantsJSONRu from './views/projects/business_advisor/business_school/participants/lang/ru.json';
export const ParticipantsKz = JSON.parse(JSON.stringify(participantsJSONKz));
export const ParticipantsRu = JSON.parse(JSON.stringify(participantsJSONRu));


//Groups
import groupsJSONKz from './views/projects/business_advisor/business_school/groups/lang/kz.json';
import groupsJSONRu from './views/projects/business_advisor/business_school/groups/lang/ru.json';
export const GroupsKz = JSON.parse(JSON.stringify(groupsJSONKz));
export const GroupsRu = JSON.parse(JSON.stringify(groupsJSONRu));


//Localities
import regionsJSONKz from './views/projects/business_advisor/business_school/regions/lang/kz.json';
import regionsJSONRu from './views/projects/business_advisor/business_school/regions/lang/ru.json';
export  const regionsKz =JSON.parse(JSON.stringify(regionsJSONKz));
export  const regionsRu =JSON.parse(JSON.stringify(regionsJSONRu));


//Holidays
import holidaysJSONKz from './views/projects/business_advisor/business_school/holidays/lang/kz.json';
import holidaysJSONRu from './views/projects/business_advisor/business_school/holidays/lang/ru.json';
export  const holidaysKz =JSON.parse(JSON.stringify(holidaysJSONKz));
export  const holidaysRu =JSON.parse(JSON.stringify(holidaysJSONRu));


export const btKz = JSON.parse(JSON.stringify(btrainerKz));
export const btRu = JSON.parse(JSON.stringify(btrainerRu));

