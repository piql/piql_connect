import { shallowMount, createLocalVue } from "@vue/test-utils"
import Vuex from "vuex"
import Listing from "@/views/settings/Listing/Listing";
import users from "@/store/settings/users";


const $t = (s)=> s
const $route = { name: 'test', query:{ page: 1} }

const localVue = createLocalVue()

localVue.use(Vuex)



describe("Listing", ()=>{
  //set mock for vuex
  let actions
  let getters
  let state
  let store

  beforeEach(() => {
    state = {
        users: null,
        pageMeta: null,
        response: null
    
    }

    actions = {
      fetchUsers: jest.fn(),
      postNewUser: jest.fn(),
      disableUserRequest: jest.fn(),
      enableUserRequest: jest.fn()
      
    }

    store = new Vuex.Store({
      modules: {
        users: {
          state,
          actions,
          getters: users.getters
        }
      }
    })
  })
    
    

    it("should render page", async ()=> {
        let wrapper = shallowMount(Listing, {
            mocks:{
              $t,
              $route,
              store,
              localVue
            },
            stubs: {
              'page-heading': true,
              'user-listing': true,
              'pager':true,
              'b-modal': true,
              'b-button':true,
    
          }
          });

          wrapper.vm.$nextTick();
    
          expect(await wrapper.exists()).toBeTruthy();

    })


})