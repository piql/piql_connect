import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from 'vuex';
import GroupListing from "@/components/GroupsListing";
import users from '@/store/settings/users';
import groups from '@/store/settings/groups';
import roles from '@/store/settings/roles';

const localVue = createLocalVue();
localVue.use( Vuex );

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;

describe("GroupsListing.vue", ()=> {

    let actions;
    let getters;
    let store;

    beforeEach( () => {
        let usersActions = {
            fetchSelectUsers: jest.fn()
        }

        let rolesActions = {
            fetchSelectedRoles: jest.fn()
        }

        store = new Vuex.Store({
            modules: {
		users: {
                    actions: usersActions,
                    getters: users.getters,
                    namespaced: true,
		},
		groups: {
                    actions,
                    getters: groups.getters,
                    namespaced: true,
		},
		roles: {
                    actions: rolesActions,
                    getters: roles.getters,
                    namespaced: true,
		},
	    }
        });
    } );

    test("render groups listing component", async ()=>{

        let wrapper = shallowMount(GroupListing, {
            store,
            localVue,
            mocks:{
                $route,
                $t
            },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'vue-select-sides': true,
                'pager': true
            },
            propsData:{
                height: 1
            }
        });

        await localVue.nextTick();
        expect(wrapper.exists()).toBeTruthy();
    })
})
