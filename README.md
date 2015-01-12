# Marketo Leads

Create a lead in Marketo from any form!

*Requires >=PHP 5.3.3 & MCrypt*

## Usage

Link a Marketo field with any number of form fields so that when that field is submitted it is sent to Marketo:

- Follow Marketo's [REST API Quick Start Guide](http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/)
- Enter API details into `WP Admin > Marketo Fields > Options` and save
- Click `Add New` to create a field for each Marketo Field you want to send data to

### Definitions

- **Marketo field** is the name of the field within Marketo (Marketo > Admin > Field Management > Export Field Names)
- **Form fields** are the `name`s of form fields in HTML

### Finding form field `name`s (for non-technical people)

Enable Debug mode in the options page and submit a form. Info about the submitted data will be displayed, including a section called "Post Data" which shows the form field `name`s and their values.

## Notes

- Fields starting with `_wp` will be ignored
- In the form fields box, you can add a comment after a slash to remind you where the field came from, e.g. `field_name / Contact form`.

## Hooks

The "Hooks" option allows you to define when the plugin will try to create a lead. If left to the default value of `wp_loaded`, whenever form data is submitted which matches fields set in the admin area, the plugin will try to create a lead. By setting this option to a hook exposed by your form builder you can prevent the plugin creating a lead unless the submission was succesful.

For example, if you use the Ninja Forms plugin, the `ninja_forms_post_process` hook will run when a form is submitted succesfully and without any validation errors. By setting the plugin to use the `ninja_forms_post_process` hook the lead will only be created if the form is submitted succesfully and all fields are valid.

You can set multiple hooks (one per line) and the plugin will try to create a lead at each one. This means that to use a hook exposed by your form builder(s) you must remove the default value first, otherwise a lead will be created regardless (because the `wp_loaded` hook is run every time).

### Example Hooks

Here are success hooks for a few popular form builders:

- Ninja Forms: `ninja_forms_post_process`
- Gravity Forms: `gform_after_submission`
- Contact Form 7: `wpcf7_before_send_mail`

### Hooks for Developers

The remaining sections are hooks exposed by the plugin that developers can use to extend Marketo Leads.

### Actions

- `rj_ml_before_create_lead`: Immediately before creating a lead
- `rj_ml_after_create_lead`: Immediately after creating a lead

### Filters

- `rj_ml_lead`: Lead data before creating lead/debug
- `rj_ml_options`: Options before creating lead/debug
- `rj_ml_hooks`: Array of hooks to run on
- `rj_ml_save_options`: Options before they are saved
- `rj_ml_capability`: Capability required to edit Marketo options