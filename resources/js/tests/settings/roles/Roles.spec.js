import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from "vuex";
import Roles from '@/views/settings/Admin/Account/Roles/Roles';

const localVue = createLocalVue();
localVue.use( Vuex );

const $t = (s) => s;
const errorToast = jest.fn();
const successToast = jest.fn()

describe("Roles.vue", ()=> {
    let actions;
    let getters;
    let store;

    beforeEach(() => {
        actions = {
            fetchUsers: jest.fn(),
        }

        getters = {
            rolesApiResponse: () => {},
            rolesPageMeta: () => {},
            formattedUsers: () => [ {value: 123, label: 'Test Name'}],
        }

        store = new Vuex.Store({
            actions,
            getters
        });
    })


    //test whether component renders
    test("roles component should render", ()=> {
        let wrapper = shallowMount(Roles, {
            localVue,
            store,
            mocks: { $t },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'role-items': true,
                'page-heading': true,
                'pager': true,
            }
        });

        expect(wrapper.exists()).toBeTruthy();
    })


    //test force Render
    test("role key should increment by 1 for every method call", ()=> {
        let wrapper = shallowMount(Roles, {
            localVue,
            store,
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
