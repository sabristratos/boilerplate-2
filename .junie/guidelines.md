Of course. Here is the component documentation in raw Markdown format.

# Flux UI Component Guidelines

This document provides a quick reference guide for using Flux UI components in your Laravel Blade and Livewire projects. It summarizes the primary syntax and available props for each component based on the provided documentation.

### **General Principles**

* **Shorthand Props:** Most form controls (`input`, `select`, `textarea`, etc.) support `label` and `description` props as a shorthand for automatically wrapping the component in a `<flux:field>`.
* **Livewire Integration:** Use `wire:model` to bind component state to your Livewire properties. For performance, use `wire:model.defer` for form inputs and `wire:model.live` for controls that should update immediately (like tabs or switches).
* **Icons:** Icons are used via dot notation (e.g., `<flux:icon.bolt />`). Most components that accept an `icon` prop will use the icon name (e.g., `icon="bolt"`).

---

## **Layout & Structure**

### `flux:card`

A container for related content.

**Syntax**

```blade
<flux:card class="space-y-6">
    <flux:heading size="lg">Card Title</flux:heading>
    <flux:text>Card content goes here.</flux:text>
</flux:card>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `size` | `sm` for a more compact card. |

### `flux:accordion`

A container for collapsible content sections.

**Syntax**

```blade
<flux:accordion>
    <flux:accordion.item heading="First Item">
        Content for the first item.
    </flux:accordion.item>
    <flux:accordion.item heading="Second Item" :expanded="true">
        Content for the second item.
    </flux:accordion.item>
</flux:accordion>
```

**Props: `flux:accordion`**
| Prop | Description |
| :--- | :--- |
| `exclusive` | `true` to only allow one item to be open at a time. |
| `variant` | `reverse` to display the icon before the heading. |

**Props: `flux:accordion.item`**
| Prop | Description |
| :--- | :--- |
| `heading` | Shorthand for the item's heading text. |
| `expanded` | `true` to expand the item by default. |
| `disabled` | `true` to prevent the item from being opened. |

### `flux:tabs`

A component for organizing content into separate panels.

**Syntax**

```blade
<flux:tab.group>
    <flux:tabs wire:model.live="activeTab">
        <flux:tab name="profile" icon="user">Profile</flux:tab>
        <flux:tab name="account" icon="cog-6-tooth">Account</flux:tab>
    </flux:tabs>

    <flux:tab.panel name="profile">
        Profile content...
    </flux:tab.panel>
    <flux:tab.panel name="account">
        Account content...
    </flux:tab.panel>
</flux:tab.group>
```

**Props: `flux:tabs`**
| Prop | Description |
| :--- | :--- |
| `wire:model` | Binds the active tab's `name` to a Livewire property. |
| `variant` | Visual style. Options: `default`, `segmented`, `pills`. |
| `size` | `sm` for smaller tabs (only for `segmented` variant). |

**Props: `flux:tab`**
| Prop | Description |
| :--- | :--- |
| `name` | Unique identifier to match with a `flux:tab.panel`. |
| `icon` | Name of an icon to display. |
| `disabled` | `true` to disable the tab. |

---

## **Form Components**

### `flux:field`

A wrapper for form controls that provides structure for labels, descriptions, and errors.

**Syntax**

```blade
<flux:field>
    <flux:label badge="Required">Email</flux:label>
    <flux:description>Enter your primary email address.</flux:description>
    <flux:input wire:model.defer="email" type="email" />
    <flux:error name="email" />
</flux:field>
```

**Props: `flux:label`**
| Prop | Description |
| :--- | :--- |
| `badge` | Optional text to display as a badge (e.g., "Required"). |

**Props: `flux:error`**
| Prop | Description |
| :--- | :--- |
| `name` | The name of the field to display validation errors for. |

### `flux:input`

The standard component for text-based inputs.

**Syntax**

```blade
<flux:input
    wire:model.defer="username"
    label="Username"
    description="This will be publicly displayed."
    placeholder="e.g., john_doe"
    icon="user"
/>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `wire:model` | Binds the input to a Livewire property. |
| `label` | Shorthand for the field label. |
| `description` | Shorthand for the help text. |
| `type` | Input type (`text`, `email`, `password`, `file`, `date`). |
| `placeholder` | Placeholder text. |
| `icon` / `icon:trailing` | Name of an icon to prepend/append. |
| `size` | `sm`, `xs`. |
| `invalid` | `true` to apply error styling. |
| `disabled` / `readonly` | `true` to disable or make read-only. |
| `clearable` | `true` to add a clear button. |
| `copyable` | `true` to add a copy-to-clipboard button. |
| `viewable` | `true` for a password visibility toggle. |
| `kbd` | A keyboard shortcut hint (e.g., "⌘K"). |
| `class:input`| Applies classes directly to the underlying `<input>` element. |

### `flux:textarea`

For multi-line text input.

**Syntax**

```blade
<flux:textarea
    wire:model.defer="notes"
    label="Order Notes"
    placeholder="Any special instructions..."
    :rows="5"
/>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `rows` | Number of visible text lines. Use `"auto"` for auto-sizing. |
| `resize`| Controls resizing. Options: `vertical`, `horizontal`, `both`, `none`. |
| ... | All other relevant props from `flux:input` are also available. |

### `flux:select`

A dropdown selection component.

**Syntax (Native)**

```blade
<flux:select wire:model.defer="industry" label="Industry">
    <flux:select.option value="design">Design Services</flux:select.option>
    <flux:select.option value="dev">Web Development</flux:select.option>
</flux:select>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `variant` | `default` (native), `listbox` (Pro), `combobox` (Pro). |
| `multiple` | `true` to allow multiple selections (`listbox`/`combobox` only). |
| `searchable` | `true` to add a search input (`listbox`/`combobox` only). |
| `clearable` | `true` to add a clear button. |
| ... | All other relevant props from `flux:input` are also available. |

### `flux:radio.group`

For selecting a single, mutually exclusive option.

**Syntax**

```blade
<flux:radio.group wire:model.defer="plan" label="Select Plan">
    <flux:radio value="basic" label="Basic" description="For personal use." />
    <flux:radio value="pro" label="Pro" description="For professionals." />
</flux:radio.group>
```

**Props: `flux:radio.group`**
| Prop | Description |
| :--- | :--- |
| `variant` | Visual style. Options: `default`, `segmented`, `cards` (Pro). |

**Props: `flux:radio`**
| Prop | Description |
| :--- | :--- |
| `value` | The value associated with the radio option. |
| `label` | The display label for the option. |
| `description` | Help text for the option. |
| `checked` | `true` to select by default. |

### `flux:checkbox`

For selecting one or more options.

**Syntax**

```blade
<flux:checkbox.group wire:model.defer="notifications" label="Notifications">
    <flux:checkbox value="email" label="Email" />
    <flux:checkbox value="sms" label="SMS" />
</flux:checkbox.group>
```

**Props: `flux:checkbox.group`**
| Prop | Description |
| :--- | :--- |
| `variant` | Visual style. Options: `default`, `cards` (Pro). |

**Props: `flux:checkbox`**
| Prop | Description |
| :--- | :--- |
| `value` | The value to include in the `wire:model` array when checked. |
| `label` | The display label for the checkbox. |
| `indeterminate` | `true` for a "select all" checkbox that is partially checked. |
| `checked` | `true` to check by default. |

### `flux:switch`

A binary toggle control. Recommended for auto-saving settings outside of forms.

**Syntax**

```blade
<flux:switch
    wire:model.live="notifications"
    label="Enable Notifications"
    description="Receive updates about your account."
/>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `wire:model` | Binds the on/off state (boolean) to a Livewire property. |
| `label` | The label for the switch. |
| `description` | Help text for the switch. |
| `align`| Alignment relative to the label. Options: `right` (default), `left`. |
| `disabled`| `true` to disable the switch. |

---

## **Action & Navigation Components**

### `flux:button`

The primary component for user actions.

**Syntax**

```blade
<flux:button wire:click="save" variant="primary" icon="check">
    Save Changes
</flux:button>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `variant` | Visual style. Options: `outline`, `primary`, `filled`, `danger`, `ghost`, `subtle`. |
| `size` | Size. Options: `base` (default), `sm`, `xs`. |
| `icon` / `icon:trailing` | Name of an icon to prepend/append. |
| `href` | Renders the button as an `<a>` tag. |
| `loading` | `false` to disable the automatic loading indicator. |
| `disabled` | `true` to disable the button. |
| `tooltip` | Adds a tooltip on hover. |

### `flux:button.group`

Visually groups multiple buttons together.

**Syntax**

```blade
<flux:button.group>
    <flux:button>Years</flux:button>
    <flux:button>Months</flux:button>
    <flux:button>Days</flux:button>
</flux:button.group>
```

### `flux:dropdown`

A container for dropdown menus.

**Syntax**

```blade
<flux:dropdown align="end">
    <flux:button icon:trailing="chevron-down">Options</flux:button>
    <flux:menu>
        <flux:menu.item icon="pencil-square" kbd="E">Edit</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
    </flux:menu>
</flux:dropdown>
```

**Props: `flux:dropdown`**
| Prop | Description |
| :--- | :--- |
| `position` | `top`, `right`, `bottom` (default), `left`. |
| `align` | `start` (default), `center`, `end`. |

**Props: `flux:menu.item`**
| Prop | Description |
| :--- | :--- |
| `icon` | Name of an icon to display. |
| `kbd` | Keyboard shortcut hint. |
| `variant`| `default`, `danger`. |
| `disabled`| `true` to disable the item. |

### `flux:modal`

A dialog that appears over the main content.

**Syntax**

```blade
{{-- Trigger --}}
<flux:modal.trigger name="delete-confirm">
    <flux:button variant="danger">Delete</flux:button>
</flux:modal.trigger>

{{-- Modal --}}
<flux:modal name="delete-confirm" class="md:w-96">
    <flux:heading>Are you sure?</flux:heading>
    <flux:text class="mt-2">This action cannot be undone.</flux:text>
    <div class="mt-4 flex justify-end gap-2">
        <flux:modal.close>
             <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="delete" variant="danger">Confirm</flux:button>
    </div>
</flux:modal>
```

**Props: `flux:modal`**
| Prop | Description |
| :--- | :--- |
| `name` | Unique identifier for the modal, used by triggers. |
| `wire:model` | Bind the modal's open state to a Livewire property. |
| `variant` | `default`, `flyout`. |
| `position` | For `flyout`: `right` (default), `left`, `bottom`. |
| `:dismissible` | `:dismissible="false"` to prevent closing on outside click. |

---

## **Display Components**

### `flux:badge`

A component to highlight information.

**Syntax**

```blade
<flux:badge color="lime" size="sm" icon="check-circle">Success</flux:badge>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `color` | Color from the Tailwind palette (e.g., `zinc`, `red`, `lime`, `blue`). |
| `variant` | `default`, `solid`, `pill`. |
| `size` | `sm`, `default`, `lg`. |
| `icon` / `icon:trailing` | Name of an icon to prepend/append. |

### `flux:avatar`

Displays a user's image or initials.

**Syntax**

```blade
<flux:avatar src="https://..." tooltip="User Name" />
<flux:avatar name="Caleb Porzio" color="auto" />
```

**Props**
| Prop | Description |
| :--- | :--- |
| `src` | URL to the avatar image. |
| `name` | Used to generate initials if `src` is not provided. |
| `initials`| Manually specify initials. |
| `size`| `xs`, `sm`, `default`, `lg`, `xl`. |
| `circle`| `true` for a circular avatar. |
| `tooltip`| `true` or a string to show a tooltip on hover. |
| `badge`| `true` for a dot indicator, or a string for text content. |
| `color` | Color for initials background (e.g., `red`, `blue`, `auto`). |

### `flux:callout`

A component to highlight important information.

**Syntax**

```blade
<flux:callout icon="information-circle" variant="outline">
    <flux:callout.heading>This is important</flux:callout.heading>
    <flux:callout.text>
        Please review the updated terms of service.
        <flux:callout.link href="#">Learn more</flux:callout.link>
    </flux:callout.text>
</flux:callout>
```

**Props**
| Prop | Description |
| :--- | :--- |
| `icon` | Name of an icon to display. |
| `variant` | `outline`, `success`, `warning`, `danger`. |
| `color` | Any Tailwind color to override the variant. |
| `