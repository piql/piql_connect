import { shallowMount } from "@vue/test-utils";
import GroupListing from "@/components/GroupsListing"
import flushPromises from "flush-promises"

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;

jest.mock("axios", () => ({
    get: () => Promise.resolve({ data: { data:[{ val: 1 }], meta: {val:2} } })
  }));



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

        await flushPromises();
        
        expect(wrapper.exists()).toBeTruthy();

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

        await flushPromises();

        expect(wrapper.vm.list.length).toBe(1);

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

        await flushPromises();

        expect(wrapper.vm.ulist.length).toBe(1);
        

        

    })
})