import { shallowMount } from "@vue/test-utils"
import UserListing from "@/components/UserListing"
import { VBTooltip } from 'bootstrap-vue';


const fns = {
  convertTime:(isoDate)=>{
    let date = new Date(isoDate);
    let year = date.getFullYear();
    let month = date.getMonth()+1;
    let dt = date.getDate();
    let time = date.getHours() + ':'+ date.getMinutes();

    if (dt < 10) {
    dt = '0' + dt;
    }
    if (month < 10) {
    month = '0' + month;
    }

    return year+'-' + month + '-'+dt + ' '+time;

    }
}



const $t = (s)=>s;
const users = [
  {
    full_name: 'Sewalu Mukasa Steven',
    username: 'smstoroc',
    email: 'smstoroc@gmail.com',
    disabled: false,
    created_at: '2020-07-17T11:42:17.000000Z'
  },
  {
    full_name: 'Kyobe James',
    username: 'jkyobe',
    email: 'jkyobe@gmail.com',
    disabled: false,
    created_at: '2020-07-17T11:42:17.000000Z'
  }
];


describe("UserListing.vue", ()=>{

  let wrapper = shallowMount(UserListing, {
    mocks:{
      $t
    
    },
    stubs:{
      'pager':true,
      'b-modal': true,
      'b-button':true,
      'b-badge':true,
      'b-tooltip': true
    },
    propsData:{
      users: users
    }
  });
  


  test("should render when users props is passed", ()=> {
    
    expect(wrapper.vm.users).toBe(users);
  })

  test("the component should renders", ()=> {
   
    expect(wrapper.exists()).toBeTruthy();

  })

  test("the format date function should work", ()=>{
    let isoDate = "2020-07-17T11:42:17.000000Z";
    let returnDate = fns.convertTime(isoDate);
    expect(wrapper.vm.formatDate(isoDate)).toBe(returnDate);
  })

})