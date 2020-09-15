import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from "vuex"
import Template from "@/views/settings/Admin/Account/Metadata/Template.vue";
import DublinCoreTemplate from "@/components/DublinCoreTemplate.vue";
import MetadataTemplateList from "@/components/MetadataTemplateList.vue";


const $t = (s)=> s
const $route = { name: 'test', query:{ page: 1} }

const localVue = createLocalVue()
localVue.use(Vuex)


describe("Template", ()=>{
    let actions;
    let getters;
    let state;
    let store;

    beforeEach( () => {

        actions = {
        }

        getters = {
            templates: () => [],
            templateById: () => {}
        };

        store = new Vuex.Store({
            actions,
            getters
        });

    });

    test("should render its page with components", async ()=> {
        let wrapper = shallowMount(Template, {
            localVue,
            store,
            mocks:{
                $t,
                $route,
            },
            components: {
                DublinCoreTemplate,
                MetadataTemplateList
            },
            stubs: {
                'page-heading': true,
                'breadcumb': true
            }
        });

        await wrapper.vm.$nextTick();

        expect(wrapper.exists()).toBeTruthy();
        expect(wrapper.findComponent(DublinCoreTemplate).exists()).toBeTruthy();
        expect(wrapper.findComponent(MetadataTemplateList).exists()).toBeTruthy();
    })
})
