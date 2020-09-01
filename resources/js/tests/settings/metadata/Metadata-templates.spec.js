import { shallowMount } from "@vue/test-utils";
import Template from "@/views/settings/Admin/Account/Metadata/Template.vue";
import DublinCoreTemplate from "@/components/DublinCoreTemplate.vue";
import MetadataTemplateList from "@/components/MetadataTemplateList.vue";


const $t = (s)=> s
const $route = { name: 'test', query:{ page: 1} }

describe("Template", ()=>{

    test("should render its page with components", async ()=> {
        let wrapper = shallowMount(Template, {
            mocks:{
                $t,
                $route
            },
            components: {
                DublinCoreTemplate,
                MetadataTemplateList
            },
            stubs: {
                'page-heading': true
            }
        });

        await wrapper.vm.$nextTick();

        expect(wrapper.exists()).toBeTruthy();
        expect(wrapper.findComponent(DublinCoreTemplate).exists()).toBeTruthy();
        expect(wrapper.findComponent(MetadataTemplateList).exists()).toBeTruthy();
    })
})
