<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src=" //unpkg.com/vue-router@3.0.0/dist/vue-router.js"></script> 
   
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   
 <!-- Add this after vue.js -->
    <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>
    <script src="//unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
   
    
    <title>Инвентарная книга</title>
</head>
<body>

<!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    
<div id="app">
  
<?php 
//<menu></menu>
include "menu.php"; 

?>
    
    <h1>Инвентарная книга</h1>

    
        <table>
        <tr v-for="item in items" >
        <td>{{item.id}}</td><td>{{item.name}}</td><td>{{item.charact}}</td><td>{{item.count}}</td>
        </tr>
        </table>
    

    <!-- Button trigger modal -->
<!--  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalAct">
  Launch demo modal
</button> -->

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
           <!--
                <tr valign="top">
                  <td><input type="text" v-model="rekv.name" /></td>
                  <td><textarea rows="3" v-model="rekv.charact" ></textarea></td> 
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
-->
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
<script type="module" src="/templates/frontend/js/invbook.js" >
  
</script>
</body>
</html>