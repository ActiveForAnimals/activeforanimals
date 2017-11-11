## How should a CSV file be formatted?

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

1 | start_date | end_date | address | address_extra_information | title | description | results
- | ---------- | -------- | ------- | ------------------------- | ----- | ----------- | -------
2 | 2016-12-13 11:00 | 2016-12-13 13:00 | Kultorvet, Copenhagen, Denmark | By the fountain | My custom title | My custom description | leafleting \| 4 \| 0 \| 1 \| 0 \| 1000 \| Flyer design B

### Start date and end date
Required  
Dates are required for each event and must match the
[ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format: YYYY-MM-DD HH:MM.  
Example: 2016-12-13 11:00

### Address
The address of an event should be a proper address.  
Any extra location information, such as *"By the fountain"*, *"Room B"*, etc.
that aren’t part of a real address should be added to the
**address_extra_information** column instead. If possible, use addresses with
city and country appended.  
Example: Grenzacherstrasse 10, Basel, Switzerland

### Results
Results consist of six values:
- import name of result
- participant count
- duration in minutes, hours, days
- quantifiable result value

Values are separated by the "vertical bar" character ( | ). Each row can contain
another result for the same event.  
Example: leafleting | 9 | 30 | 2 | 0 | 4000  
*This reads: a leafleting result | 9 participants | duration: 30 minutes |
2 hours | 0 days | 4000 leaflets*
