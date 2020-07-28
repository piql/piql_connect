import { shallowMount } from "@vue/test-utils";
import Listing from "../../views/settings/Listing/Listing";
import axios from "axios"
import Pager from "../../components/Pager"

const $t = function(s){ return s; }
const $route = function(){ return  { name: 'test' }; }

describe("Listing", ()=>{
  
    
    
    it("user listing page should render", ()=> {

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
      
      let endpoint = "/api/v1/admin/users";
      let queryString ="?page=1";

      //get users request
      wrapper.vm.refreshObjects(queryString,endpoint)


      expect(wrapper.vm.response).toBeDefined;

    })


})