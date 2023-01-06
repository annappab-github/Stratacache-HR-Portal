import Login from './components/Login.vue';
import Register from './components/Register.vue';
import ManageUsers from './views/ManageUsers.vue';
import ManageRole from './views/ManageRoles.vue';
import UserRoles from './views/UserRoles.vue';
import UserProfile from './views/UserProfile.vue';
import Report from './views/Report.vue';
import EditUsers from './views/EditUsers.vue';
import CreateUser from './components/CreateUser.vue';
import EditUserRole from './components/EditUserRole.vue';
import ForgotPasswordReset from './components/ForgotPasswordReset.vue';
import Home from './views/Home.vue';
import MyInfo from './views/MyInfo.vue';
import EditMyInfo from './views/EditMyInfo.vue';
import People from './views/People.vue';
import Files from './views/Files.vue';
import Help from './views/Help.vue';
import FileUpload from './views/FileUpload.vue';
import passwordChange from './views/passwordChange.vue';
import LinkExpired from './views/LinkExpired.vue';
import Subfiles from './components/Subfiles.vue';
import { routeNames } from './js/routeNames';
import { decryptData } from './js/utils/encrypt';
import { createRouter, createWebHashHistory } from 'vue-router';
// import AdminDashboard from './components/Dashboard/AdminDashboard.vue';
import DirectReport from './views/DirectReport';
import EditEmployees from './views/EditEmployees.vue';
import { getCookie, removeAllLocalStorage, getLocalStorageByName } from './js/composables';
import EmployeeReport from './components/Reports.vue';
// import AssignedLeave from './components/AssignedLeave.vue';
import Team from './components/Team.vue';
import LeaveAdjustment from './components/LeaveAdjustment.vue';


//Decrypting Permissions
// let mkLocalData = localStorage.getItem('Permissions');
// if (!mkLocalData) {
//     mkLocalData = 'null';
// }


const secretPermissionKey = process.env.KEY || 'myscecretkey';
// var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);


const routes = [{
        path: '/userprofile',
        name: routeNames.userProfile,
        component: UserProfile,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },
    },
    {
        path: '/report',
        name: routeNames.report,
        component: Report,
    },
    {
        path: '/leaves',
        name: routeNames.leaves,
        component: LeaveAdjustment,
    },
    {
        path: '/',
        name: routeNames.login,
        component: Login,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next({ path: '/home' });
            else {
                removeAllLocalStorage();
                next();
            }
        },
    },

    {
        path: '/home',
        name: routeNames.home,
        component: Home,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else
                removeAllLocalStorage();
            next({ path: '/' });
        },
    },
    {
        path: '/passwordChange',
        name: routeNames.passwordChange,
        component: passwordChange,
        beforeEnter: (to, from, next) => {
            if (getCookie('pwdResetToken') == '')
                next({ path: '/' });
            else {
                next();
            }
        },
    },
    {
        path: '/reset-password/:token/:email',
        name: routeNames.resetPassword,
        component: ForgotPasswordReset,
        meta: {
            auth: false
        }
    },
    {
        path: '/help',
        name: routeNames.help,
        component: Help,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },
    },
    // {
    //     path: '/assignedto',
    //     name: routeNames.assignedto,
    //     component: AssignedLeave,
    // },
    {
        path: '/team',
        name: routeNames.team,
        component: Team,
    },
    {
        path: '/register',
        name: routeNames.register,
        component: Register,
    },
    {
        path: '/linkexpired',
        name: routeNames.linkexpired,
        component: LinkExpired,
    },

    {
        path: '/fileupload',
        name: routeNames.fileupload,
        component: FileUpload,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('File-Access-Permission'))
                next({ path: '/home' });
            else {
                next();
            }
        },
    },
    {
        path: '/people',
        name: routeNames.People,
        component: People,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },
    },
    {
        path: '/files',
        name: routeNames.Files,
        component: Files,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },
    },
    {
        path: '/myinfo',
        name: routeNames.myInfo,
        component: MyInfo,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },

    },
    {
        path: '/myinfo/edit/:empid?',
        name: routeNames.editMyInfo,
        component: EditMyInfo,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },

    },
    {
        path: '/directreport/:id?',
        name: routeNames.directreport,
        component: DirectReport,
        props: true,
        beforeEnter: (to, from, next) => {
            if (getCookie('token') != '')
                next();
            else {
                removeAllLocalStorage();
                next({ path: '/' });
            }
        },

    },
    {
        path: '/manageuser',
        name: routeNames.manageUser,
        component: ManageUsers,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('View-User'))
                next({ path: '/home' });
            else {
                next();
            }
        },
    },
    {
        path: '/manageroles',
        name: routeNames.manageRole,
        component: ManageRole,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('View-Role'))
                next({ path: '/home' });
            else {
                next();
            }
        },
    },
    {
        path: '/edituser/:userid',
        name: routeNames.editUser,
        component: EditUsers,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('Edit-User'))
                next({ path: '/manageuser' });
            else {
                next();
            }
        },
    },
    {
        path: '/editemployee/:empid?',
        name: routeNames.editEmployee,
        component: EditEmployees,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('Edit-User'))
                next({ path: '/people' });
            else {
                next();
            }
        },
    },

    {
        path: '/subfiles/:foldername',
        name: routeNames.subfiles,
        component: Subfiles,
    },
    {
        path: '/newroles',
        name: routeNames.newUserRole,
        component: UserRoles,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('Create-Role'))
                next({ path: '/manageroles' });
            else {
                next();
            }
        },
    },
    {
        path: '/editroles/:roleid',
        name: routeNames.editUserRole,
        component: EditUserRole,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('Edit-Role'))
                next({ path: '/manageroles' });
            else {
                next();
            }
        },
    },
    {
        path: '/newuser',
        name: routeNames.newUser,
        component: CreateUser,
        beforeEnter: (to, from, next) => {
            var permissions = decryptData(getLocalStorageByName('Permissions'), secretPermissionKey);
            if (getCookie('token') == '') {
                removeAllLocalStorage();
                next({ path: '/' });
            } else if (!permissions.includes('Create-User'))
                next({ path: '/manageuser' });
            else {
                next();
            }
        },
    },
    {
        path: '/employee-report',
        name: routeNames.employeeReport,
        component: EmployeeReport,
    },
    // {
    //   path: '/dashboard',
    //   name: routeNames.adminDashboard,
    //   component: AdminDashboard,
    // },
];

const router = createRouter({
    history: createWebHashHistory(process.env.BASE_URL),
    routes
});

router.beforeEach((to, from, next) => {

    if (
        to.name !== routeNames.login && getCookie('token') == '' && getCookie('pwdResetToken') == '' && to.name !== routeNames.resetPassword && to.name !== routeNames.linkexpired
    ) {
        next({ name: routeNames.login });
    } else next();
});


export default router;