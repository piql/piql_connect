import Groups from "@/views/settings/Admin/Account/Group"
import Vuex from 'vuex'
import { shallowMount, createLocalVue } from "@vue/test-utils"

const localVue = createLocalVue();
localVue.use( Vuex );

const $route = { name: 'test', query: { page: 1 }}
const $t = (s)=>s;

describe("Group.vue", ()=> {

    let actions;
    let getters;
    let store;

    beforeEach( () => {
        actions = {
            fetchGroups: jest.fn(),
            fetchUserSettings: jest.fn()
        };

        getters = {
            groupsApiResponse: () => {},
	    groupsPageMeta: () => {},
	    userTableRowCount: () => 10,
            formattedUsers: () => [ {value: 123, label: 'Test Name'}],
        };

        store = new Vuex.Store({
            actions,
            getters
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


    //test force Render
    test("group key should increment by 1 for every method call", ()=> {
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

        wrapper.vm.forceRerender();


        expect(wrapper.vm.groupkey).toBe(1);
    })

    test("list user objects for selection", async ()=>{
        let wrapper = shallowMount(Groups, {
            store,
            localVue,
            mocks:{
                $route,
                $t
            },
            stubs:{
                'b-modal': true,
                'b-button': true,
                'groups-listing': true,
                'page-heading': true
            },
            propsData:{
                height: 1
            }
        });

        let ulist = await wrapper.vm.formattedUsers;
        expect(ulist.length).toBe(1);
    })

})
