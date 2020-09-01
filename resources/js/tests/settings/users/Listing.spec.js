import { Store } from 'vuex-mock-store'
import { shallowMount} from "@vue/test-utils"
import Listing from "@/views/settings/Listing/Listing";


const $t = (s)=> s
const $route = { name: 'test', query:{ page: 1} }

// create the Store mock
const store = new Store({
  state: { 
    users: [{name: 'Test User'}],
    pageMeta: {page: 1},
    response: {status: 'test response'}
   },
  getters: { 
    formattedUsers: [{name: 'Test User'}] ,
    usersPageMeta: {page: 1},
    userApiResponse: {status: 'test response'}
  },
})
const $store = store;
const errorToast = jest.fn();
const successToast = jest.fn()

// reset spies, initial state and getters
afterEach(() => store.reset())



describe("Listing", ()=>{
  
  let wrapper
  beforeEach(() => {
    wrapper = shallowMount(Listing, {
      mocks:{
        $t,
        $route,
        $store,
        errorToast,
        successToast
      },
      stubs: {
        'page-heading': true,
        'user-listing': true,
        'pager':true,
        'b-modal': true,
        'b-button':true,
        'Pager': true

    }
    })
  })

    it("should render page", async ()=> {
        //let wrapper = shallowMount(Listing, );

          wrapper.vm.$nextTick();
    
          expect(await wrapper.exists()).toBeTruthy();

    })


})