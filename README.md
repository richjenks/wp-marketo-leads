# Marketo Leads

Create a lead in Marketo from any form!

*Requires >=PHP 5.3.3*

## Usage

Link a Marketo field with any number of form fields so that when that field is submitted it is sent to Marketo:

- Follow Marketo's [REST API Quick Start Guide](http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/)
- Enter API details into `WP Admin > Marketo Fields > Options` and save
- Click `Add New`

### Definitions

- **Marketo field** is the name of the field within Marketo (Marketo > Admin > Field Management > Export Field Names)
- **Form fields** are the `name`s or `id`s of form field in HTML (right-click on the field > Inspect Element)

### Finding form field `name`s/`id`s (for non-technical people)

- With the form in your browser, right-click on the field > **Inspect Element**
- In the bottom panel that appears, the selected line should look something like: `<input type="text" name="first_name">`
- The value of the `name` attribute ("first_name" in the example above) is the name of the field element

*If the field with have no `name`, use its `id` instead.*

## Notes

- Fields starting with `_wp` will be ignored
- In the form fields box, you can add a comment after a slash to remind you where the field came from, e.g. `field_name / Contact form`.

## Hooks

### Actions

- `rj_ml_lead_created`: Immediately after creating a lead, receives lead data and options arrays

### Filters

- `rj_ml_lead`: Constructed lead data
- `rj_ml_options`: Options array