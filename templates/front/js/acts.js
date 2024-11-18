import {setCookie, getCookie, PlatformMixin} from './common.js';

const Acts = {
    template: `
<div>
    <table>
        <tr><th>№ Акта</th><th>Дата</th><th>Представитель</th><th>Составитель</th></tr>
        <tr v-for="item in items" >
            <td>{{item.id}}</td><td>{{item.create_dt}}</td><td>{{item.delegate}}</td><td>{{item.former}}</td>
        </tr>
    </table>
</div>
`,
    data(){
        return { 
        items:[],
        error_mess: ''
        }
    },
    mounted(){ 
        document.title = 'Акты'; 
        this.loadItems(); 
    },
    mixins: [PlatformMixin],
    methods: {
        loadItems() 
        {
            this.dataQuery({
              method: 'get',
              url: `/api/v1/acts`
            }, response=> 
              {
              this.items = response.data
              console.log(response.data)
              }, response=> console.log(response))
        }, 
    }
}

export {Acts}; 