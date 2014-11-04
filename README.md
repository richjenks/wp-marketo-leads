# Marketo Leads

Allows WordPress forms to create leads within Marketo.

## Requirements

- PHP >=5.3.3
- cURL

## Usage

WP Marketo Leads links a Marketo field with any number of form fields so when that field is submitted it is sent to Marketo as part of a lead.

- Follow Marketo's [REST API Quick Start Guide](http://developers.marketo.com/blog/quick-start-guide-for-marketo-rest-api/)
- Enter API details into `Admin > Marketo Leads > Options` and save
- Click Add New for Marketo Leads


### Definitions

- **Title** is the Marketo field (Marketo > Admin > Field Management > Export Field Names)
- **Extract** holds the form field `name`s

### Finding field `name`s (for non-technical people)

- In Chrome, right-click on the form field and click **Inspect Element**
- In the bottom panel that appears, the selected line should look something like: `&lt;input type="text" name="first_name"&gt;`
- The value of the `name` attribute (currently "first_name") is the name of the field element

## Notes

- Fields starting with `_wp` will be ignored
- You can add a comment to a form field name (perhaps to remind you which form it's from) by adding a forward slash and your comment after the name. Note that spaces around the slash will be ignored. Example: `field_name / Contact form`