import interceptorSetup from './utils/interceptor';

export const routeNames = {
    newUser: 'new-user',
    createPermission: 'create-permission',
    managePermission: 'manage-permission',
    newUserRole: 'new-user-role',
    editUserRole: 'edit-user-role',
    editUser: 'edit-user',
    manageRole: 'manage-role',
    manageUser: 'manage-user',
    login: 'login-user',
    register: 'register-user',
    userProfile: 'user-profile',
    report: 'report',
    adminDashboard: 'admin-dashboard',
    home: 'home',
    myInfo: 'myinfo',
    People: 'people',
    Files: 'files',
    help: 'help',
    passwordChange: 'passwordChange',
    resetPassword: 'reset-password-form',
    linkexpired: 'linkexpired',
    directreport: 'directreport',
    fileupload: 'fileupload',
    fileslist: 'fileslist',
    subfiles: 'subfiles',
    assignedto: 'assignedto',
    editEmployee: 'editemployee',
    team: 'team',
    editMyInfo: 'editmyinfo',
    leaves: 'leaves',
    employeeReport: 'employee-report'
};

interceptorSetup();