import { shallowMount } from "@vue/test-utils";
import Listing from "../../../views/settings/Listing/Listing";
import axios from "axios"

const $t = function(s){ return s; }
const $route = { name: 'test', query:{ page: 1} }

describe("Listing", ()=>{
  
    
    
    test("should be able to list users", async ()=> {

      let wrapper = shallowMount(Listing, {
        mocks:{
          $t,
          $route
        },
        stubs: {
          'page-heading': true,
          'user-listing': true,
          'pager':true,
          'b-modal': true,
          'b-button':true,

      }
      });

      //expect(wrapper.exists()).toBeTruthy();
      
      let endpoint = "/api/v1/admin/users";
      let queryString ="?page=1";

      //get users request
      await wrapper.vm.refreshObjects(queryString,endpoint)

      wrapper.vm.$nextTick();


      expect(wrapper.vm.users).toBeTruthy();

    })

    test("should render page", async ()=> {
        let wrapper = shallowMount(Listing, {
            mocks:{
              $t,
              $route
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