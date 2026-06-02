# FormFlowBundle

## Rendering a form flow

A form flow only ever builds the **current step** plus its navigator, so the form itself
is rendered like any other Symfony form:

```twig
{{ form(form) }}
```

Everything else — progress indicators, step lists, breadcrumbs, sidebars — is *navigation
chrome* that you render around the form using the `form_flow_*` Twig functions. Pass the
flow view (the variable you render with `form()`) to every function.

### Cursor helpers (flat / position information)

| Function | Returns | Notes |
|----------|---------|-------|
| `form_flow_total_steps(form)` | `int` | Number of steps |
| `form_flow_steps(form)` | `string[]` | Flat, ordered step names |
| `form_flow_step_index(form)` | `int` | 0-based index of the current step |
| `form_flow_current_step(form)` | `string` | |
| `form_flow_next_step(form)` / `form_flow_previous_step(form)` | `?string` | `null` at the last/first step |
| `form_flow_first_step(form)` / `form_flow_last_step(form)` | `string` | |
| `form_flow_is_first_step(form)` / `form_flow_is_last_step(form)` | `bool` | |
| `form_flow_can_move_next(form)` / `form_flow_can_move_back(form)` | `bool` | |

```twig
{# "Step 2 of 3" #}
Step {{ form_flow_step_index(form) + 1 }} of {{ form_flow_total_steps(form) }}

{# Navigation buttons #}
{% if form_flow_can_move_back(form) %}{{ form_widget(form.navigator.previous) }}{% endif %}
{% if form_flow_can_move_next(form) %}
    {{ form_widget(form.navigator.next) }}
{% else %}
    {{ form_widget(form.navigator.finish) }}
{% endif %}
```

### Nested-step helpers

| Function | Returns | Notes |
|----------|---------|-------|
| `form_flow_root_steps(form)` | `string[]` | Top-level step names |
| `form_flow_parent_step(form, step?)` | `?string` | Defaults to the current step |
| `form_flow_child_steps(form, step?)` | `string[]` | Direct children |
| `form_flow_ancestor_steps(form, step?)` | `string[]` | Root → direct parent (breadcrumb) |
| `form_flow_step_depth(form, step?)` | `int` | 0 at the top level |
| `form_flow_is_group(form, step?)` | `bool` | A group is a non-visitable container |
| `form_flow_step_info(form, step?)` | `array` | Full per-step metadata (see below) |

The optional `step` argument defaults to the current step. An unknown step name throws.

## Which approach should I use?

**Position / progress / a single breadcrumb → use the helper functions.** They are the most
direct way to get a value:

```twig
{# Breadcrumb of the current (possibly nested) step #}
{% for step in form_flow_ancestor_steps(form) %}{{ step }} / {% endfor %}{{ form_flow_current_step(form) }}
```

**Rendering the whole tree (a stepper / sidebar) → iterate the `steps` view variable**, not the
helpers. `FormFlowType` exposes a ready-made nested tree in `form.vars.steps` (and a
skip-filtered `form.vars.visible_steps`). Each node already carries the state you need, so a
single recursive macro renders the entire hierarchy:

```twig
{% macro steps(nodes) %}
    {% import _self as self %}
    <ul>
    {% for name, step in nodes %}
        <li class="{{ step.is_current_step ? 'current' }} {{ step.is_group ? 'group' }}">
            {{ name }}
            {% if step.visible_children is not empty %}{{ self.steps(step.visible_children) }}{% endif %}
        </li>
    {% endfor %}
    </ul>
{% endmacro %}

{% import _self as self %}
{{ self.steps(form.vars.visible_steps) }}
```

Each node in `steps` / `visible_steps` provides:
`name`, `level`, `index`, `position`, `is_current_step`, `is_before_current_step`,
`is_after_current_step`, `has_current_step_descendant`, `can_be_skipped`, `is_skipped`,
`is_group`, `children`, `visible_children`.

Why prefer the tree variable for full rendering? Walking it is a single pass that preserves
hierarchy and visibility. Rebuilding the same tree from `form_flow_root_steps()` +
`form_flow_child_steps()` works (and produces identical output), but it performs repeated
name-based lookups and forces you to fetch each node's state separately via
`form_flow_step_info()`. Reserve the helpers for targeted questions ("what is the parent of
this step?", "is this a group?", "how deep is it?") and use `form.vars.steps` to draw the map.
