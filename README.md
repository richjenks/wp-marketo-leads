# Marketo Leads

Allows WordPress forms to create leads within Marketo.

## Usage

WP Marketo Leads links a Marketo field with any number of form fields so when that field is submitted it is sent to Marketo as a lead.

- Follow Marketo's [REST API Quick Start Guide](http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/) until you have determined the Endpoint URL
- Enter values for fields above and Save Changes
- Add a new field, entering the Marketo field name and form field name(s)

### Definitions

- **Marketo Field**: The name of the field within Marketo — see Marketo > Admin > Field Management > Export Field Names
- **Form field name(s)**: The name or id attribute of the input element — found by inspecting the form's HTML

### Finding field `name`s (for non-technical people)

- In Chrome, right-click on the form field and click **Inspect Element**
- In the bottom panel that appears, the selected line should look something like: `&lt;input type="text" name="first_name"&gt;`
- The value of the `name` attribute (currently "first_name") is the name of the field element

## Notes

- Fields starting with `_wp` will be ignored
- You can add a comment to a form field name (perhaps to remind you which form it's from) by adding a forward slash and your comment after the name. Note that spaces around the slash will be ignored. Example: `field_name / Contact form`