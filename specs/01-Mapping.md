# Table of Contents
- [Foreword](#foreword)
- [Appendix A: Samples](#appendix-a-samples)
	- [Local Mapping Sample (YAML)](#local-mapping-sample-yaml)

# Foreword
The Mapping suite aims to define a more developer friendly, abstract interface between Salesforce's SOAP API and the
programmer. As the suite evolves, it's individual pieces will be documented here in a way that the casual consumer
will be able to understand and implement.

Most of the Mapping suite takes it's inspiration from the [Doctrine Project](http://www.doctrine-project.org/), and
intentionally aims to keep it's specification as similar as possible so that those familar with the semantics of the
Doctrine Project can more easily work with the Mapping suite.

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