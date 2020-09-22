

/* Mock the axios service - works great with shallow mounted components */
jest.mock( 'axios', () => ({
    get: jest.fn( () => Promise.resolve({ data: 10000 }) ),
}));
import axios from 'axios';

/* mount is for integrated tests, shallowMount and createLocalVue for stubbed components,
 * RouterLinkStub is a builtin for stubbing the RouterLink-component */
import { mount, shallowMount, createLocalVue, RouterLinkStub } from '@vue/test-utils';
import VueRouter from 'vue-router';

/* import other components for integrated tests */
import Notifications from '@/components/Notifications.vue';
import SessionTimeoutMonitor from '@/components/SessionTimeoutMonitor.vue';

/* import the component to test */
import TopBar from '@/partials/TopBar.vue';

/* Some home cooked test fakes */
const get = function(){ return Promise.resolve({ data: 10000 }); }
const $t = function(s){ return s; }
const $route = function(){ return  { name: 'test' }; }

/* We can also the window global Echo service - for now this is the simplest solution
 * It can be used with component integrated tests (normal mount) */
window.Echo = {
        private: function(){
            return { listen: function() {
                return '';
            } };
        }
}

import Vuex from "vuex"


const localVue = createLocalVue();
localVue.use( Vuex )

describe( 'TopBar', () => {

    let actions;
    let getters;
    let store;

    beforeEach( () => {
        actions = {
            logoutUser: jest.fn(),
            
        };

        getters = {
            userName: () => [ {value: 123, label: 'Test Name'}],
        };

        store = new Vuex.Store({
            actions,
            getters
        });
    } );

    test('it mounts integrated with nested components', () => {
        const wrapper = mount( TopBar, {
            store,
            localVue,
            mocks: {
                $t,
                get,
                $route
            },
            components: {
                Notifications,
                SessionTimeoutMonitor
            },
            stubs: {
                'router-link': RouterLinkStub,
            }
        });
    });

    test('it mounts shallow with stubbed components', () => {
        const mocks ={
            $route,
            $t,
        };

        const wrapper = shallowMount( TopBar, {
            store,
            localVue,
            mocks,
            stubs: {
                RouterLink: RouterLinkStub,
                'session-timeout-monitor': true,
                'notifications': true,
            }
        });
        expect( wrapper.vm ).toBeTruthy();
    });

    test('is inactive for pages that are inactive', () => {
        const mocks ={
            $route,
            $t,
        };

        const wrapper = shallowMount( TopBar, {
            store,
            localVue,
            mocks,
            stubs: {
                RouterLink: RouterLinkStub,
                'session-timeout-monitor': true,
                'notifications': true,
            }
        });
        expect( wrapper.find('.navbar').classes()).toContain('top-inactive');
    });

    test('is active for the selected page', () => {

        const $route = { name: 'ingest.uploads' };
        const mocks ={
            $route,
            $t,
        };

        const wrapper = shallowMount( TopBar, {
            store,
            localVue,
            mocks,
            stubs: {
                RouterLink: RouterLinkStub,
                'session-timeout-monitor': true,
                'notifications': true,
            }
        });
        expect( wrapper.findComponent({ ref: 'nav-ingest' }).classes()).toContain('top-active');
    });

    test('is inactive for a page thats not selected', () => {

        const $route = { name: 'ingest.uploads' };
        const mocks ={
            $route,
            $t,
        };

        const wrapper = shallowMount( TopBar, {
            store,
            localVue,
            mocks,
            stubs: {
                RouterLink: RouterLinkStub,
                'session-timeout-monitor': true,
                'notifications': true,
            }
        });
        const inactives = wrapper.findAll('.navbar').filter( c => !c.classes('top-active') );
        expect( inactives.at(1).classes() ).toContain('top-inactive');
    });

});

