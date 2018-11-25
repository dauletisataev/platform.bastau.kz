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
import participantsJSONKz from './views/participants/lang/kz.json';
import participantsJSONRu from './views/participants/lang/ru.json';
export const ParticipantsKz = JSON.parse(JSON.stringify(participantsJSONKz));
export const ParticipantsRu = JSON.parse(JSON.stringify(participantsJSONRu));
