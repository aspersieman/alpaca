import Vue from 'vue';
import store from '@/store'
import Router from 'vue-router';
import Home from '@/modules/front/home/Index';
import Index from '@/modules/App';
import NotFound from '@/modules/NotFound';
import FrontHome from '@/modules/front/home/Index';
import Dashboard from '@/modules/front/dashboard/Index';
import Tasks from '@/modules/front/tasks/Index';
import Reports from '@/modules/front/reports/Index';
import Notes from '@/modules/front/notes/Index';
import AdminDashboard from '@/modules/admin/dashboard/Home';
import AdminUsers from '@/modules/admin/users/Users';
import AdminUserList from '@/modules/admin/users/components/UserLists';
import AdminUserAdd from '@/modules/admin/users/components/UserFormAdd';
import AdminUserEdit from '@/modules/admin/users/components/UserFormEdit';
import AdminUserGroupList from '@/modules/admin/users/components/GroupLists';
import AdminUserGroupAdd from '@/modules/admin/users/components/GroupFormAdd';
import AdminUserGroupEdit from '@/modules/admin/users/components/GroupFormEdit';
import AdminUserPermissionList from '@/modules/admin/users/components/PermissionLists';
import AdminUserPermissionAdd from '@/modules/admin/users/components/PermissionFormAdd';
import AdminUserPermissionEdit from '@/modules/admin/users/components/PermissionFormEdit';
import AdminFiles from '@/modules/admin/files/Files';
import AdminSettings from '@/modules/admin/settings/Settings';

Vue.use(Router);

function guard(to, from, next){
    if(store.getters['auth/isAuthenticated']) {
        next(); // allow to enter route
    } else {
        next('/');
    }
}

const router = new Router({
    mode: process.env.CORDOVA_PLATFORM ? "hash" : "history",
    base: process.env.BASE_URL,
    routes: [
        {
            name: "index",
            path: "/",
            component: Home,
        },
        {
            name: "home",
            path: "/home",
            component: Index,
            redirect: { name: "index.home" },
            children: [
                {
                    name: "index.home",
                    path: "",
                    component: FrontHome
                },
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    beforeEnter: guard,
                    component: Dashboard
                },
                {
                    path: '/tasks',
                    name: 'tasks',
                    beforeEnter: guard,
                    component: Tasks
                },
                {
                    path: '/reports',
                    name: 'reports',
                    beforeEnter: guard,
                    component: Reports
                },
                {
                    path: '/notes',
                    name: 'notes',
                    beforeEnter: guard,
                    component: Notes
                },
                {
                    name: "admin.dashboard",
                    path: "/admin/dashboard",
                    beforeEnter: guard,
                    component: AdminDashboard,
                },
                {
                    path: "/admin/users",
                    component: AdminUsers,
                    beforeEnter: guard,
                    children: [
                        {
                            name:"admin.users.list",
                            path:"/",
                            component: AdminUserList
                        },
                        {
                            name:"admin.users.create",
                            path:"create",
                            component: AdminUserAdd
                        },
                        {
                            name:"admin.users.edit",
                            path:"edit/:id",
                            component: AdminUserEdit,
                            props: (route) => ({propUserId: route.params.id}),
                        },
                        {
                            name:"admin.users.groups.list",
                            path:"groups",
                            component: AdminUserGroupList
                        },
                        {
                            name:"admin.users.groups.create",
                            path:"groups/create",
                            component: AdminUserGroupAdd
                        },
                        {
                            name:"admin.users.groups.edit",
                            path:"groups/edit/:id",
                            component: AdminUserGroupEdit,
                            props: (route) => ({propGroupId: route.params.id}),
                        },
                        {
                            name:"admin.users.permissions.list",
                            path:"permissions",
                            component: AdminUserPermissionList
                        },
                        {
                            name:"admin.users.permissions.create",
                            path:"permissions/create",
                            component: AdminUserPermissionAdd
                        },
                        {
                            name:"admin.users.permissions.edit",
                            path:"permissions/edit/:id",
                            component: AdminUserPermissionEdit,
                            props: (route) => ({propPermissionId: route.params.id}),
                        },
                    ]
                },
                {
                    name: "admin.files",
                    path: "/admin/files",
                    component: AdminFiles,
                },
                {
                    name: "admin.settings",
                    path: "/admin/settings",
                    component: AdminSettings,
                }
            ]
        },
        {
            // catch all 404 - define at the very end
            name: "notfound",
            path: "*",
            component: NotFound
        },
    ],
});

export default router;
