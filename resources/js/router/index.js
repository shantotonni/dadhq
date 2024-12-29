import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../views/auth/Login.vue'
import Main from '../components/layouts/Main'
import Dashboard from '../views/dashboard/Index.vue'
import StudentList from "../views/students/Index"
import UserList from '../views/users/Index'
import MenuList from '../views/menu/List'
import MenuPermission from '../views/users/MenuPermission'
import SliderList from '../views/slider/Index'
import ContactList from '../views/contact/Index'
import PartnerList from '../views/partner/Index'
import EventList from '../views/event/Index'
import ProgramList from '../views/program/Index'
import UserProgramList from '../views/program/UserProgram'
import CustomerList from '../views/customer/Index'
import InstructorList from '../views/instructor/Index.vue'
import InstructorsDetails from '../views/instructor/Details.vue'

import NotFound from '../views/404/Index';
import {baseurl} from '../base_url'

Vue.use(VueRouter);

const config = () => {
    let token = localStorage.getItem('token');
    return {
        headers: {Authorization: `Bearer ${token}`}
    };
}
const checkToken = (to, from, next) => {
    let token = localStorage.getItem('token');
    if (token === 'undefined' || token === null || token === '') {
        next(baseurl + 'login');
    } else {
        next();
    }
};

const activeToken = (to, from, next) => {
    let token = localStorage.getItem('token');
    if (token === 'undefined' || token === null || token === '') {
        next();
    } else {
        next(baseurl);
    }
};

const routes = [
    {
        path: baseurl,
        component: Main,
        redirect: {name: 'Dashboard'},
        children: [
            //DASHBAORD
            {
                path: baseurl + 'dashboard',
                name: 'Dashboard',
                component: Dashboard
            },
            //SESSION SETTINGS

            {path: baseurl + 'student-list', name: 'StudentList', component: StudentList},
            {path: baseurl + 'user-list', name: 'UserList', component: UserList},

            //menu vue route
            {path: baseurl + 'menu-list', name: 'MenuList', component: MenuList},
            {path: baseurl + 'user-menu-permission', name: 'UserMenuPermission', component: MenuPermission},
            //slider
            {path: baseurl + 'slider-list', name: 'SliderList', component: SliderList},
            //partners
            {path: baseurl + 'partner-list', name: 'PartnerList', component: PartnerList},
            //event
            {path: baseurl + 'event-list', name: 'EventList', component: EventList},
            //event
            {path: baseurl + 'program-list', name: 'ProgramList', component: ProgramList},
            //event
            {path: baseurl + 'customer-list', name: 'CustomerList', component: CustomerList},
            {path: baseurl + 'contact-list', name: 'ContactList', component: ContactList},
            //user program
            {path: baseurl + 'user-program', name: 'UserProgramList', component: UserProgramList},
            {path: baseurl + 'instructor-list', name: 'InstructorList', component: InstructorList},
            {path: baseurl + 'instructor-details/:id', name: 'InstructorsDetails', component: InstructorsDetails},
        ],
        beforeEnter(to, from, next) {
            checkToken(to, from, next);
        }
    },
    {
        path: baseurl + 'login',
        name: 'Login',
        component: Login,
        beforeEnter(to, from, next) {
            activeToken(to, from, next);
        }
    },
    {
        path: baseurl + '*',
        name: 'NotFound',
        component: NotFound,
    },
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.baseurl,
    routes
});

router.afterEach(() => {
    $('#preloader').hide();
});

export default router
