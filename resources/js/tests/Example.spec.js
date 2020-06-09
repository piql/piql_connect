import { mount } from '@vue/test-utils';
import ExampleComponent from '@/components/ExampleComponent.vue';

test( 'it works', () => {
    expect( !false ).toBe( true );
})

test( 'ExampleComponent should mount', () => {
    const wrapper = mount ( ExampleComponent );
    expect( wrapper ).toMatchSnapshot();
});
