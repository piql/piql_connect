import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from 'vuex'
import GroupListing from "@/components/GroupsListing"

const localVue = createLocalVue();
localVue.use( Vuex );

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;

describe("GroupsListing.vue", ()=> {

    let actions;
    let getters;
    let store;

    beforeEach( () => {
        actions = {
            fetchGroups: jest.fn(),
            fetchGroupUsers: jest.fn(),
            fetchGroupRoles: jest.fn(),
            fetchSelectUsers: jest.fn(),
            fetchSelectedRoles: jest.fn(),
        };

        getters = {
            groupsApiResponse: () => {},
            userGroups: () => [{ id: 345, name: "Test Group Name", description: "Group description for testing"}],
            groupPageMeta: () => {},
            userGroupUsers: () => [ {id : 123, full_name: 'Test Name'}],
            userGroupRoles: () => [ {id: 1, name: 'A test role'}],
            formattedUsers: () => [ {value: 123, label: 'Test Name'}],
            userRoles: () => [ {value: 123, label: 'Test Name'}],
        };

        store = new Vuex.Store({
            actions,
            getters
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
                'pager': true,
                'breadcumb': true
            },
            propsData:{
                height: 1
            }
        });

        await localVue.nextTick();
        expect(wrapper.exists()).toBeTruthy();
    })

    test("list role objects for selection", async ()=>{
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
        })

        let list = await wrapper.vm.list;
        expect(list.length).toBe(1);
    })

    test("list user objects for selection", async ()=>{
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

        let ulist = await wrapper.vm.ulist;
        expect(ulist.length).toBe(1);
    });
})
