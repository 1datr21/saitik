//const axios = require('axios'); 
//const { createApp, ref, onMounted } = Vue
//import VueRouter from 'vue-router'

import {setCookie, getCookie, PlatformMixin} from './common.js';

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

const Invbook = {
    template: `
<div>    
         <h1>Инвентарная книга</h1>

    
   <div class="d-grid gap-2 d-md-block">
          <button type="button" @click="loadInvBook" class="btn btn-primary">Обновить</button>

          <button type="button" data-bs-toggle="modal" data-bs-target="#ModalAct" class="btn btn-primary">Акт приемки-продажи</button>
          
    </div>
  
        <table>
        <tr v-for="item in items" >
        <td>{{item.id}}</td><td>{{item.name}}</td><td>{{item.charact}}</td><td>{{item.count}}</td>
        </tr>
        </table>
  
<!-- Modal -->
<div class="modal fade" id="ModalAct" tabindex="-1" aria-labelledby="ModalActLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalActLabel">Акт приемки-передачи</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form >
          <div class="alert alert-danger" role="alert" v-if="error_mess!=''">
             {{ error_mess }}
          </div>
          <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Представитель</label>
            <input type="text" class="form-control" id="exampleFormControlInput1" v-model="act.Delegate" />
          </div>
          <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Составитель</label>
            <input type="text" class="form-control" id="exampleFormControlTextarea1"  v-model="act.Former"></textarea>
          </div>
          <h1>Передаваемые реквизиты <button @click.prevent="addInv">+</button></h1>

          <div v-for="(rekv, idx) in act.items">
            <div class="mb-3 row">
              
              <div class="col-sm-5">
                <input type="text"  class="form-control-plaintext" v-model="rekv.name"  placeholder="Наименование" />
              </div>
              <button class="col-sm-1 btn btn-outline-primary btn-sm" @click.prevent="deleteInv(idx)">x</button>
              <button class="col-sm-1 btn btn-outline-primary btn-sm" @click.prevent="moveDown(idx)">↓</button>
              <button class="col-sm-1 btn btn-outline-primary btn-sm" @click.prevent="moveUp(idx)">↑</button>
            </div>
            <div class="mb-3 row">
              <label for="exampleFormControlTextarea1" class="form-label">Описание</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" v-model="rekv.charact"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" @click.prevent="SaveAct()">Сохранить</button>
      </div>
    </div>
  </div>
  </div>

</div>
</div>
    `,
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
    mounted(){ document.title = 'Инвентарная книга'; this.loadInvBook(); },
    created () {
      
  }
  };
  
export {Invbook}; 