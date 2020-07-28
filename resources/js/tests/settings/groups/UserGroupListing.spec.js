import { shallowMount } from "@vue/test-utils";
import GroupListing from "../../../components/GroupsListing"

//import axios from "axios"

const $route = { name: 'test', query: { page: 1 }}



describe("GroupsListing.vue", ()=> {

    test("render groups listing component", async ()=>{

        let wrapper = shallowMount(GroupListing, {
            mocks:{
                $route
            },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'vue-select-sides': true,
            },
            propsData:{
                height: 1
            }
        })

        wrapper.vm.$nextTick();
        
        expect(await wrapper.exists()).toBeTruthy();

    })
})