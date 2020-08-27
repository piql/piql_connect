import { Store } from 'vuex-mock-store'
import Roles from '@/views/settings/Roles/Roles'
import { shallowMount } from "@vue/test-utils"

const $t = (s) => s;

// create the Store mock
const store = new Store({
    state: { 
      roles: [{name: 'Test Roles'}],
      pageMeta: {page: 1},
      response: {status: 'test response'}
     },
    getters: { 
      userRoles: [{name: 'Test Roles'}] ,
      rolesPageMeta: {page: 1},
      rolesApiResponse: {status: 'test response'}
    },
  })
  const $store = store;
  const errorToast = jest.fn();
  const successToast = jest.fn()

describe("Roles.vue", ()=> {
    let wrapper = shallowMount(Roles, {
        mocks: { 
            $t,
            $store,
            errorToast,
            successToast
         },
        stubs:{
            'b-modal': true,
            'b-button': true,
            'role-items': true,
            'page-heading': true,
            'Pager': true
        } 
    });

    //test whether component renders
    test("roles component should render", ()=> {
        expect(wrapper.exists()).toBeTruthy();
    })


    //test force Render
    test("role key should increment by 1 for every method call", ()=> {
        
        wrapper.vm.forceRerender();
        expect(wrapper.vm.rolekey).toBe(1);
    })

})