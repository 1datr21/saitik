import { PlatformMixin, setCookie, getCookie } from './common.js'

import { Invbook } from './invbook.js'
import { Acts } from './acts.js'

/*
<div class=" navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><router-link to="/login"  class="nav-link" >Home</router-link></li>
            <li class="nav-item"><router-link to="/" class="nav-link" >Invbook</router-link></li>
            <li class="nav-item"><router-link to="/contact"  class="nav-link" >Contact</router-link></li>
        </ul>
    </div>
*/
const app = Vue.createApp({
   //{{User.login}}  
    data(){
      return {
        currApp: null,      
        Authed: false
      }
    },
    // Authed
    template: `
<div>   

    <nav v-if="Authed" class="navbar navbar-expand-lg navbar-light bg-light" >
      <div class="container-fluid">      
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">          
              <router-link to="/"  class="nav-link" >Главная</router-link>
            </li>        
            <li class="nav-item">
              <router-link to="/acts"  class="nav-link" >Акты</router-link>
            </li>
          </ul>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <li class="d-flex"> Hello,&nbsp;{{User.login}}  </li>
            <li class="d-flex">
              <a class="nav-link" href="#" @click.prevent="logout" >Выход</a>
            </li>
        </div>
      </div>
    </nav>  

    <router-view></router-view>    
</div>
    `,
    created()
    {
      this.currApp = Vue.getCurrentInstance().appContext.app;
      this.load_pages();   
    },
    mounted()
    {      
    
      if(localStorage.refresh_token==undefined)
      {
        this.$router.push('/login');
      }
    },
    computed: {
       /* Authed: {
          get: ()=>{
            return localStorage.refresh_token!=undefined;
          }
        },*/
        User: {
          get: ()=> { 
            try{
              return JSON.parse(localStorage.user); 
            }
            catch(Exc)
            {
              return {'login':''};
            }
          }
        }
    },
    methods: {
        load_pages(){
            const routes = [                
              {
                path: "/login",
                name: "Login",
                component: Login,
                access: ()=> { return localStorage.refresh_token==undefined; }
              },
              {
                    path: '/',
                    name: "Invbook",
                    component: Invbook,
                    access: () => { return localStorage.refresh_token!=undefined; }
              },
              {
                    path: '/acts',
                    name: "Acts",
                    component: Acts,
                    access: ()=> { return localStorage.refresh_token!=undefined; }
              }    
            ];
      
            if(this.$router)
            {
                for(let route of routes){
                    var access = route.access();
                    if(access)
                    {
                        if(!this.$router.hasRoute(route.name))
                        {
                          this.$router.addRoute(route);
                        }
                    }
                    else
                    {
                        if(this.$router.hasRoute(route.name))
                        {
                          this.$router.removeRoute(route.name);
                        }
                    }
                }
            }
            else
            {
              var acc_routes = [];
              for(let route of routes)
              {
                var access = route.access();
                if(access) { acc_routes.push(route);  }  
              }

              const router = VueRouter.createRouter({
                history: VueRouter.createWebHistory(),
                routes: acc_routes
              });
      
              //Vue.getCurrentInstance().appContext.
              this.currApp.use(router);              
            }
            this.Authed = localStorage.refresh_token!=undefined;
        },

        logout() {
            if(confirm('Выйти из аккаунта'))
            {
                axios.get(`/api/v1/auth/logout`, {headers : { Refresh : localStorage.refresh_token }}).then((resp) => 
                {                                  
                    localStorage.clear();   
                    setCookie('access_token', null); 
                    //this._computedWatchers.Authed.run();
                    //this.$watch.call('Authed');
                    this.load_pages();
                    this.$router.push('/login');
                    this.Authed = false;
                    this.$forceUpdate();
                }).catch( err => {  
                    console.log('Could not logout');
                })
            }
          
        }
    }    

});

import { Login } from './login.js'

//import { createWebHistory, createRouter } from "vue-router";
const Home = {template : `
<div>
    <h1>Home</h1>
    <p></p>
</div>    
`};
const About = {template : `
<div>
    <h1>About</h1>
    <p>Lorem, ipsum, blya</p>
</div>    
`};








// указываем объекту приложения использовать объект маршрутизатора
app.mount('#app');