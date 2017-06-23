# Imports

On this page:

- [What is an import?](#what-is-an-import)
- [How do I import events?](#how-do-i-import-events)
- [How should a CSV file be formatted?](#how-should-a-csv-file-be-formatted)
- [Timezones and dates?](#timezones-and-dates)

## <a name="what-is-an-import"></a>What is an import?

You may want to create many events at once, for example from Google Spreadsheets. To accomplish this, you can create a CSV file and import it to your group.

## <a name="how-do-i-import-events"></a>How do I import events?

To import events, go to your group page and click on the link *Manage imports*. On the import overview page for your group, click on the link *Import events from a CSV file*.  
You can also go to [activeforanimals.com/import/csv](/import/csv?tour=1) and follow the instructions.

## <a name="how-should-a-csv-file-be-formatted"></a>How should a CSV file be formatted?

A CSV file must adhere to the following format.  
The first row must contain the following column names:

- start_date
- end_date
- address
- address_extra_information
- title
- description
- results

The rows after the first row contain the events to be imported.  
Example:

| 1 | start_date | end_date | address | address_extra_information | title | description | results |
| - | ---------- | -------- | ------- | ------------------------- | ----- | ----------- | ------- |
| 2 | 2016-12-13 11:00 | 2016-12-13 13:00 | Kultorvet, Copenhagen, Denmark | By the fountain | My custom title | My custom description | leafleting \| 4 \| 0 \| 1 \| 0 \| 1000 \| Flyer design B |


### Start date and end date
Required  
Dates are required for each event and must match the [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format: YYYY-MM-DD HH:MM.  
Example: 2016-12-13 11:00

### Address
The address of an event should be a proper address.  
Any extra location information, such as *"By the fountain"*, *"Room B"*, etc. that aren’t part of a real address should be added to the **address_extra_information** column instead. If possible, use addresses with city and country appended.  
Example: Grenzacherstrasse 10, Basel, Switzerland

### Results
Results consist of six values:
- name of result
- participant count
- duration in minutes, hours, days
- quantifiable result value

Values are separated by the "vertical bar" character ( | ). Each row can contain another result for the same event.  
Example: leafleting | 9 | 30 | 2 | 0 | 4000  
*This reads: a leafleting result | 9 participants | duration: 30 minutes | 2 hours | 0 days | 4000 leaflets*

## <a name="timezones-and-dates"></a>Timezones and dates

The dates of imported events will be imported relative to the selected groups timezone. For example, if the imported event has a start time of 11:00 am, and the group selected for the import has the timezone "Europe/Copenhagen (UTC +1)", the start time will be 11:00 am (UTC +1).
