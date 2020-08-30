import Vue from 'vue';
import Router from 'vue-router';
import store from '../common/Store';

Vue.use(Router);

const router = new Router({
  mode: process.env.CORDOVA_PLATFORM ? "hash" : "history",
  base: process.env.BASE_URL,
  routes: [
    {
      name: "index",
      path: "/",
      component: require("../modules/App"),
      redirect: { name: "index.home" },
      children: [
        {
          name: "index.home",
          path: "",
          component: require("../modules/front/home/Home")
        },
        {
          path: '/dashboard',
          name: 'dashboard',
          component: require("../modules/front/dashboard/Dashboard")
        },
        {
          path: '/tasks',
          name: 'tasks',
          component: require("../modules/front/tasks/Tasks")
        },
        {
          name: "admin.dashboard",
          path: "/admin/dashboard",
          component: require("../modules/admin/dashboard/Home"),
        },
        {
          path: "/admin/users",
          component: require("../modules/admin/users/Users"),
          children: [
            {
              name:"admin.users.list",
              path:"/",
              component: require("../modules/admin/users/components/UserLists")
            },
            {
              name:"admin.users.create",
              path:"create",
              component: require("../modules/admin/users/components/UserFormAdd")
            },
            {
              name:"admin.users.edit",
              path:"edit/:id",
              component: require("../modules/admin/users/components/UserFormEdit"),
              props: (route) => ({propUserId: route.params.id}),
            },
            {
              name:"admin.users.groups.list",
              path:"groups",
              component: require("../modules/admin/users/components/GroupLists")
            },
            {
              name:"admin.users.groups.create",
              path:"groups/create",
              component: require("../modules/admin/users/components/GroupFromAdd")
            },
            {
              name:"admin.users.groups.edit",
              path:"groups/edit/:id",
              component: require("../modules/admin/users/components/GroupFromEdit"),
              props: (route) => ({propGroupId: route.params.id}),
            },
            {
              name:"admin.users.permissions.list",
              path:"permissions",
              component: require("../modules/admin/users/components/PermissionLists")
            },
            {
              name:"admin.users.permissions.create",
              path:"permissions/create",
              component: require("../modules/admin/users/components/PermissionFormAdd")
            },
            {
              name:"admin.users.permissions.edit",
              path:"permissions/edit/:id",
              component: require("../modules/admin/users/components/PermissionFormEdit"),
              props: (route) => ({propPermissionId: route.params.id}),
            },
          ]
        },
        {
          name: "admin.files",
          path: "/admin/files",
          component: require("../modules/admin/files/Files"),
        },
        {
          name: "admin.settings",
          path: "/admin/settings",
          component: require("../modules/admin/settings/Settings"),
        }
      ]
    },
    {
      // catch all 404 - define at the very end
      name: "notfound",
      path: "*",
      component: require("../modules/NotFound")
    },
  ],
});

router.beforeEach((to, from, next) => {
  store.commit("showLoader");
  next();
});

router.afterEach((to, from) => {
  setTimeout(()=>{
    store.commit("hideLoader");
  },1000);
});

export default router;
