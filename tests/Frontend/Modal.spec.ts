import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import Modal from '@/components/Modal.vue';

describe('Modal.vue Component', () => {
  it('renders correctly when isOpen is true', () => {
    // Wrapper di dalam teleport body tidak bisa langsung dicek pakai mount (karena ditaruh di document.body)
    // Untuk unit test sedehana yang tidak perlu document stubing teleport, kita set sebuah mock div body untuk teleport
    const el = document.createElement('div');
    el.id = 'app';
    document.body.appendChild(el);

    const wrapper = mount(Modal, {
      props: {
        isOpen: true,
        title: 'Test Modal',
      },
      slots: {
        default: '<div class="test-slot-content">Main Content</div>',
      },
      global: {
          stubs: {
              teleport: true
          }
      }
    });

    // Check prop title renders
    expect(wrapper.text()).toContain('Test Modal');
    // Check slot renders
    expect(wrapper.html()).toContain('test-slot-content');
    expect(wrapper.text()).toContain('Main Content');
    // Check close button emit
    const cancelBtn = wrapper.findAll('button').find(b => b.text().includes('Cancel'));
    expect(cancelBtn).toBeDefined();
  });

  it('does not render content when isOpen is false', () => {
    const wrapper = mount(Modal, {
      props: {
        isOpen: false,
        title: 'Hidden Modal',
      },
      global: {
          stubs: {
              teleport: true
          }
      }
    });

    expect(wrapper.text()).not.toContain('Hidden Modal');
    expect(wrapper.find('.fixed').exists()).toBe(false);
  });
});
