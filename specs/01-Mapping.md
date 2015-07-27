# Table of Contents
- [Foreword](#foreword)
- [Entity Manager](#entity-manager)
- [Field Types](#field-types)
	- [Boolean](#boolean-field-type)
	- [Date](#date-field-type)
	- [DateTime](#datetime-field-type)
	- [ID](#id-field-type)
	- [Integer](#integer-field-type)
	- [Phone](#phone-field-type)
	- [Reference](#reference-field-type)
	- [String](#string-field-type)
- [Local Mapping](#local-mapping)
- [Appendix A: Samples](#appendix-a-samples)
	- [Local Mapping Sample (YAML)](#local-mapping-sample-yaml)

# Foreword
The Mapping suite aims to define a more developer friendly, abstract interface between Salesforce's SOAP API and the
programmer. As the suite evolves, it's individual pieces will be documented here in a way that the casual consumer
will be able to understand and implement.

Most of the Mapping suite takes it's inspiration from the [Doctrine Project](http://www.doctrine-project.org/), and
intentionally aims to keep it's specification as similar as possible so that those familar with the semantics of the
Doctrine Project can more easily work with the Mapping suite.

# Entity Manager
The Mapping suite's entity manager is responsible for keeping track of the entities used by the suite, as well as their
configurations. It offers methods for loading configurations from files in various formats (such as YAML), and is the
"conduit" for communications with Salesforce.

# Field Types

## Boolean Field Type
A boolean field type may be either `true` or `false`, or the integers `1` and `0` respectively.

## DateTime Field Type
DateTime fields represent timestamps, and have a precision down to the second. Internally, DateTime fields are always
transferred in UTC, but the Mapping suite will convert PHP DateTime objects to and from UTC as necessary. Any DateTime
object that goes to Salesforce will be converted to UTC for transfer, and will be converted back to the current timezone
(as determined by the [`date_default_timezone_get`](http://php.net/manual/en/function.date-default-timezone-get.php)
PHP function) before becoming accessible to your application.

## ID Field Type
ID fields are the primary key for the object, and are always constrained to a string of 15 characters.

## Integer Field Type
An integer field is a non-decimal number. Integer fields can optionally include the `unsigned` option, which is a
boolean option that determines if the field should include a sign or not.

## Phone Field Type
Phone fields store phone numbers (duh). The value passed to a phone field may be any string, and is not required to pass
any kind of validation, but the following transformation will occur when sending the value to Salesforce.

1. All characters that are not digits or the letter "x" will be stripped
2. The number will be reformatted so that the resulting string follows the defined phone format; by default, this is:
	1. The 10 characters before the "x" character (or end of string) are formatted `(###) ###-####`
	2. Any characters more than 10 characters away from the "x" (or end of string) are considered the region code, and
	are prepended like so `## (###) ###-####`
	3. If the string contained an "x", any digits following it are considered the extension, and are appended like
	so `(###) ###-#### x####`

The region code and extension are always optional for phone fields.

When receiving phone fields from Salesforce, the opposite operation is performed. Any character that is not a digit
or the letter "x" is removed before being written to the entity. In this way, all phone numbers are normalized before
being consumed by the implementing application.

## Reference Field Type
A cross-reference to another remote object. Reference fields follow similar conventions to [ID fields](#id-field-type).

Rerference is not a valid type for entity fields, and should not be used.

## String Field Type
A string field is simply a series of characters that have a maximum length placed upon them. All string field defintions
**MUST** have a `length` definition as well.

# Local Mapping
The Local Mapping module of the Mapping suite is the abstraction layer that allows you to define how your objects in
Salesforce are constructed and behave. By default, it contains ONLY those mappings that are universal to all Salesforce
accounts (such as Account and Contact), and configured to function the way that a fresh Salesforce account would.

The default configurations can be overridden by giving the [entity manager](#entity-manager) additional configurations
that name the same remote object in their mapping.

Additionally, all entities (default or otherwise) are assumed to have the following system fields:

|Field Name|Type|Description|
|----------|----|-----------|
|Id|[ID](#id-field-type)|The globally unique string that identifies a Salesforce object|
|IsDeleted|[Boolean](#boolean-field-type)|Indicates if the record is in the recycle bin (`true`) or not (`false)|
|CreatedById|[Reference](#reference-field-type)|ID of the User that created the record|
|CreatedDate|[DateTime](#datetime-field-type)|Date and time that the record was created|
|LastModifiedById|[Reference](#reference-field-type)|ID of the User who last updated the record|
|LastModifiedDate|[DateTime](#datetime-field-type)|Date and time that the record was last updated by a User|
|SystemModstamp|[DateTime](#datetime-field-type)|Date and time that the record was last modified by the system|

More information on the default system fields
[can be found here](https://developer.salesforce.com/docs/atlas.en-us.api.meta/api/system_fields.htm).

# Appendix A: Samples

## Local Mapping Sample (YAML)
```
DaybreakStudios\Salesforce\Samples\Lead:
	remote: Lead
	fields:
		fname:
			remote: FirstName
			type: string
			length: 40
		lname:
			remote: LastName
			type: string
			length: 80
			required: true
		converted:
			remote: IsConverted
			type: boolean
			writeable: false
		phone:
			type: phone
	manyToOne:
		owner:
			target: User
		type:
			remote: RecordTypeId
			target: RecordType
	oneToOne:
		convertedAccount:
			target: Account
		convertedContact:
			target: Contact
```