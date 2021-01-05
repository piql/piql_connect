import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from "vuex";
import Roles from '@/views/settings/Admin/Account/Roles/Roles';
import users from '@/store/settings/users';
import roles from '@/store/settings/roles';

const localVue = createLocalVue();
localVue.use( Vuex );

const $t = (s) => s;

describe("Roles.vue", ()=> {
    let store;

    beforeEach(() => {
        let usersActions = {
            fetchUsers: jest.fn(),
        }

        let rolesActions = {
        }

        store = new Vuex.Store({
            modules: {
                users: {
                    actions: usersActions,
                    getters: users.getters,
                    namespaced: true
                },
                roles: {
                    actions: rolesActions,
                    getters: roles.getters,
                    namespaced: true
                },
            }
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
