import { shallowMount, createLocalVue } from "@vue/test-utils"
import Vuex from "vuex"
import Listing from "@/views/settings/Admin/Account/Users";
import users from "@/store/settings/users";
import Pager from "@/components/Pager";

const $t = (s)=> s
const $route = { name: 'test', query:{ page: 1} }

const localVue = createLocalVue();
localVue.use( Vuex );

describe("Listing", ()=>{
  let actions;
  let getters;
  let store;

  beforeEach(() => {
    actions = {
      fetchUsers: jest.fn(),
      postNewUser: jest.fn(),
      disableUserRequest: jest.fn(),
      enableUserRequest: jest.fn()
    }

    getters = {
        userApiResponse: () => {},
        usersPageMeta: () => {},
        formattedUsers: () => [ {value: 123, label: 'Test Name'}],
    }

    store = new Vuex.Store({
        actions,
        getters
      });
  })

    it("should render page", async ()=> {
        let wrapper = shallowMount(Listing, {
            localVue,
            store,
            mocks:{
              $t,
              $route,
            },
            stubs: {
              'page-heading': true,
              'user-listing': true,
              'pager':true,
              'b-modal': true,
              'b-button':true,
              'breadcumb': true
            }
          });

          wrapper.vm.$nextTick();
          expect(await wrapper.exists()).toBeTruthy();

    })


})
