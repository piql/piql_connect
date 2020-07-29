import { shallowMount } from "@vue/test-utils";
import Listing from "../../../views/settings/Listing/Listing";


const $t = function(s){ return s; }
const $route = { name: 'test', query:{ page: 1} }



describe("Listing", ()=>{
    
    

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