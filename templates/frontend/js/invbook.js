//const axios = require('axios'); 
//const { createApp, ref, onMounted } = Vue
//import VueRouter from 'vue-router'

import {setCookie, getCookie, PlatformMixin} from './main.js';

class InvInAct {
  constructor()
  {
    this.name = "";
    this.charact = "";
  }
}

class Act{
  constructor()
  {
   this.items = [];
   this.items.push(new InvInAct());
   this.Delegate = ""; // Представитель
   this.Former = ""; //Составитель
  }
}

axios.interceptors.request.use(config => {
  //config.headers.post['Authorization'] =  `Bearer ${localStorage.getItem('access_token')}`;
  config.headers.Authorization =  `Bearer ${localStorage.getItem('access_token')}`;
  return config; // Путь свободен, продолжаем выполнение запроса
});

  var app = Vue.createApp({
    mixins: [PlatformMixin],
    data(){
        return { 
          items:[],
          act: new Act(),
          error_mess: ''
         }
    },
    methods: {
        loadInvBook() 
        {
            this.dataQuery({
              method: 'get',
              url: `/api/v1/invbook`
            }, response=> 
              {
              this.items = response.data
              console.log(response.data)
              }, response=> console.log(response))
        },  
        addInv(){
          this.act.items.push(new InvInAct());
        },
        moveDown(idx){
          if(idx==this.act.items.length-1) return;
          this.act.items.splice(idx, 2, this.act.items[idx+1], this.act.items[idx]);
          
        },
        moveUp(idx)
        {
          if(idx==0) return;
          this.act.items.splice(idx-1, 2, this.act.items[idx], this.act.items[idx-1]);
        },
        deleteInv(idx){
          if(idx==0 && this.act.items.length==1) return;
          this.act.items.splice(idx,1);
        },
        SaveAct()
        {
          this.dataQuery({
            method: 'post',
            url: `/api/v1/invbook/save`, 
            data: {
              act: this.act
            }
          }, response => {
                console.log("SERVER RESPONSE");
                console.log(response);
                if(response.data.response=='error')
                {
                  this.error_mess = response.data.message;
                }
                else
                {
                    let myModal = new bootstrap.Modal(document.getElementById('ModalAct'));
                    myModal.hide();

                    this.act = new Act();
                    this.error_mess = '';
                    this.loadInvBook();
                }
            }
          );
        },
      
    },
    mounted(){ this.loadInvBook(); }
    
  });

  /*
app.component('menu', {
  template: `
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Главная</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" >Обновить</a>
        </li> 
        <li class="nav-item">
          <a class="nav-link" href="#" tabindex="-1"  data-bs-toggle="modal" data-bs-target="#ModalAct">Акт приемки-продажи</a>
        </li>
        
      </ul>
    </div>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <li class="d-flex"> Hello, {{User.login}}</li>
        <li class="d-flex">
            <a class="nav-link" href="#" @click.prevent="logout">Выход</a>
        </li>
    </div>
  </div>
</nav>  
  `,
  data(){
    return { 
      user_id: 0,
      User: JSON.parse(localStorage.user)
     }
},
  methods: {
      logout() {
          if(confirm('Выйти из аккаунта'))
          {
                  axios.get(`/api/v1/auth/logout`, {headers : { Refresh : localStorage.refresh_token }}).then((resp) => {
                  window.location = '/login';
                  localStorage.clear();   
                  setCookie('access_token', null); 
              }).catch( err => {  
                  console.log('Could not logout');
              })
          }
      }
  }  
});  */
  
  //app.mount('#app');