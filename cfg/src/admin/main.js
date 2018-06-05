import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import 'admin/assets/styles/base';
import App from './App';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

Vue.use(ElementUI, { size: 'small' });
Vue.use(VueRouter);
Vue.use(Vuex);

// 加载路由
import createRouter from './router';

const router = createRouter();

console.log('env is: ', process.env.NODE_ENV);

new Vue({
  el: '#app',
  router,
  render: h => h(App)
})


