import Groups from "@/views/settings/Admin/Account/Group"
import { shallowMount } from "@vue/test-utils"

const $t = (s) => s;

describe("Groups.vue", ()=> {
    //test whether component renders
    test("component should render", ()=> {
        let wrapper = shallowMount(Groups, {
            mocks: { $t },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'groups-listing': true,
                'page-heading': true
            } 
        });

        expect(wrapper.exists()).toBeTruthy();
    })


    //test force Render
    test("group key should increment by 1 for every method call", ()=> {
        let wrapper = shallowMount(Groups, {
            mocks: { $t },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'groups-listing': true,
                'page-heading': true
            } 
        });

        wrapper.vm.forceRerender();


        expect(wrapper.vm.groupkey).toBe(1);
    })

})
