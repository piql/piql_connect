import Groups from "@/views/settings/Admin/Account/Group";
import Vuex from 'vuex';
import { shallowMount, createLocalVue } from "@vue/test-utils";
import users from '@/store/settings/users';
import user from '@/store/settings/user';
import groups from '@/store/settings/groups';

const localVue = createLocalVue();
localVue.use( Vuex );

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;

describe("Group.vue", ()=> {

    let actions;
    let getters;
    let store;

    beforeEach( () => {
        let userActions = {
            fetchUserSettings: jest.fn()
        }

        let groupActions = {
            fetchGroups: jest.fn()
        }

        let userState = {
            settings: { interface: {  } }
        }

        store = new Vuex.Store({
            modules: {
                users: {
                    actions,
                    getters: users.getters,
                    namespaced: true,
                },
                user: {
                    state: userState,
                    actions: userActions,
                    getters: user.getters,
                    namespaced: true,
                },
                groups: {
                    actions: groupActions,
                    getters: groups.getters,
                    namespaced: true,
                },
            }
        });
    })

    //test whether component renders
    test("component should render", ()=> {
        let wrapper = shallowMount(Groups, {
	    store,
            localVue,
            mocks:{
                $t,
                $route,
            },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'groups-listing': true,
                'page-heading': true
            } 
        });

        expect(wrapper.exists()).toBeTruthy();
    })

})
