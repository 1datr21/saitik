<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
   
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   
 <!-- Add this after vue.js -->
    <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>
    <script src="//unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
  <!--   <script src="//unpkg.com/vue-cookies@1.5.12/vue-cookies.js"></script> -->
    
    <title>Войти</title>
</head>
<body>
    <div class="container" id="app">
    <div class="row">
        <div class="col">
      
        </div>
        <div class="col">
            <form action="">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Логин</label>
                <input type="text" class="form-control" name="login" v-model="login" />
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Пароль</label>
                <input type="password" class="form-control" name="password" v-model="password" />
            </div>
            <input type="submit" value="Войти" class="btn btn-outline-primary" @click.prevent="Auth()" />
            </form>
        </div>
        <div class="col">
        
        </div>
    </div>
    </div>
    
    <script type="module" src="/templates/frontend/js/login.js" ></script>
</body>
</html>