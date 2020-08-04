import { shallowMount } from "@vue/test-utils";
import GroupListing from "@/components/GroupsListing"

//import axios from "axios"

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;



describe("GroupsListing.vue", ()=> {

    test("render groups listing component", async ()=>{

        let wrapper = shallowMount(GroupListing, {
            mocks:{
                $route,
                $t

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

    test("list role objects for selection", async ()=>{
        let wrapper = shallowMount(GroupListing, {
            mocks:{
                $route,
                $t
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

        await wrapper.vm.fetchRoles(100);
        wrapper.vm.$nextTick()

        expect(typeof wrapper.vm.list).toEqual('object');

    })

    test("list user objects for selection", async ()=>{
        let wrapper = shallowMount(GroupListing, {
            mocks:{
                $route,
                $t
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

        await wrapper.vm.fetchUsers(100);
        wrapper.vm.$nextTick()

        expect(typeof wrapper.vm.ulist).toEqual('object');

    })
})