import Roles from '@/views/settings/Roles/Roles'
import { shallowMount } from "@vue/test-utils"

const $t = (s) => s;

describe("Roles.vue", ()=> {
    //test whether component renders
    test("roles component should render", ()=> {
        let wrapper = shallowMount(Roles, {
            mocks: { $t },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'role-items': true,
                'page-heading': true
            } 
        });

        expect(wrapper.exists()).toBeTruthy();
    })


    //test force Render
    test("role key should increment by 1 for every method call", ()=> {
        let wrapper = shallowMount(Roles, {
            mocks: { $t },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'role-items': true,
                'page-heading': true
            } 
        });

        wrapper.vm.forceRerender();


        expect(wrapper.vm.rolekey).toBe(1);
    })

})