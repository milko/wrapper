# Term

> A `term` is a *concept* out of *context*.

Terms are used to represent *categories*, types, *controlled vocabulaty* elements and many other objects. The term is the ancestor of most other objects in this library and all terms reside in the same collection.

A term by itself defines a *concept*, when it is used as a property or connected to another term by a predicate in a directed graph it expresses a *function*.

You can think of this as *words* in a sentence: as the *position* and *order* changes, so does their *meaning*.

#### Identification

All terms share the same set of properties used to identify the object:

All terms feature an `lid` property which represents the *local identifier* of the term. This is a ***string*** value that uniquely identifies the term within its namespace. This property is **required**.

The `ns` represents the term *namespace*, it is a *reference* to another term that represents the namespace to which the current term's *local identifier* belongs. This property is **required**.

> All user defined terms must have a namespace, however, in practice there is a set of terms that do not have a namespace, these are the core elements of the library.

The `gid` is the term's *global identifier*, it is a ***string*** value that uniquely identifies the term *among all namespaces*: it can be considered as the "official" term identifier. By default the global identifier is composed by concatenating the *global identifier* of the *namespace term* to the *local identifier* of the *current term*, separated by a colon, but this is optional and the value can be anything, provided it doesn't collide with another term's global identifier.

*[RDF]: Resource Description Framework
The *document key* of a term is a ***string*** obtained by hashing the global identifier. This is to allow for long identifiers, such as RDF identifiers, without impacting on the primary key; derived classes, however, may have different behaviours.

#### Naming

All terms must have a *name*, `name`, and a *description*, `descr`, these represents the core metadata of the object.

Both properties are implemented as an ***array*** in which the element key is a *language code* and the value is the name or description *expressed in that language*.

#### Values

As with all objects in the library, no value can be `null` or empty: in that case the property should be omitted.

Besides the above properties, a term can have any other property, provided it is defined in the data dictionary.