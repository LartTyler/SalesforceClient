# Table of Contents
- [Foreword](#foreword)
- [Field Types](#field-types)
	- [Boolean](#boolean-field-type)
	- [DateTime](#datetime-field-type)
	- [ID](#id-field-type)
	- [Reference](#reference-field-type)
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

## Reference Field Type
A cross-reference to another remote object. Reference fields follow similar conventions to [ID fields](#id-field-type).

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
		FirstName:
			type: string
			length: 128
			nullable: false
```