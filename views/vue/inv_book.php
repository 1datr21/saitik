<html>
    <head>
     <script src="https://unpkg.com/vue@3/dist/vue.global.js" ></script> 
   <!-- <script src="/vue/node_modules/vue/dist/vue.global.js" ></script> -->
    </head>
<body>
<div id="app">
    <h2>{{message}}</h2>
    <button @click="LoadData()">Update</button>
</div>
<script>
import axios from 'axios'
import VueAxios from 'vue-axios'

const vueApp = Vue.createApp({
  data() {
    return {
        message: 'Welcome to Vue.js',
        items: [],
    }
  },
  methods:{
      // методы-обработчики событий жизненного цикла
      // помещаются вне параметра methods
      LoadData: ()=>{
        this.axios.get('/api/v1/invbook', {
                    name: this.name,
                    description: this.description
                })
                .then(function (response) {
                    this.items = response.data;
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
/*
        fetch('/api/v1/invbook').then(
            function (response) { // Success.
                console.log(response.data);
                this.items = response.data;
                },
            function (response) { // Error.
                    console.log('An error occurred.');
                }
            );*/
      }
    },
    beforeCreate(){
        console.log('beforeCreate()');
    },
    created(){
        console.log('created()');
    },
    beforeMount(){
        console.log('beforeMount()');
    },
    mounted(){
        console.log('mounted()');
        this.LoadData();
    },
    beforeUpdate(){
        console.log('beforeUpdate()');
    },
    updated(){
        console.log('updated()');
    },
    beforeUnmount(){
        console.log('beforeUnmount()');
    },
    unmounted(){
        console.log('unmounted()');
    }
});
vueApp.mount('#app');
</script>
</body>
</html>