import { shallowMount } from "@vue/test-utils";
import UserListing from "../../../components/UserListing"

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

  test("should render when users props is passed", ()=> {
    


    let wrapper = shallowMount(UserListing, {
      mocks:{
        $t
      },
      stubs:{
        'pager':true,
        'b-modal': true,
        'b-button':true,
        'b-badge':true
      },
      propsData:{
        users: users
      }
    });


    expect(wrapper.vm.users).toBe(users);
  })

  test("the component should renders", ()=> {
    let wrapper = shallowMount(UserListing, {
      mocks:{
        $t
      },
      stubs:{
        'pager':true,
        'b-modal': true,
        'b-button':true,
        'b-badge':true
      },
      propsData:{
        users: users
      }
    });

    expect(wrapper.exists()).toBeTruthy();

  })

  test("the format date function should work", ()=>{
    let sampleDate = "2020-07-17T11:42:17.000000Z";

    let wrapper = shallowMount(UserListing, {
      mocks:{
        $t
      },
      stubs:{
        'pager':true,
        'b-modal': true,
        'b-button':true,
        'b-badge':true
      },
      propsData:{
        users: users
      }
    });

    expect(wrapper.vm.formatDate(sampleDate)).toBe('2020-07-17 14:42');
  })

})