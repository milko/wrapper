# Types

> `Types` are `terms` that represent *data types*, *schemas*, *structures*, *categories* and *ontologies*.

A *term* becomes a *type* when its reference in an object's property is used to indicate a data or other type, or when it is used as a node in a directed graph.

#### Data types

Data types are terms used to indicate the kind of data a property may hold, it is used in *descriptors* to provide a way of validating data. Besides being used as the value of a property, data types are organised in a directed graph representing a structure where *specialised data types* are derived from *primitive data types*, each level provides information on how to test for valid data.

![image](file://img/Example - Types.svg)

##### *Primitive data types*
These data types represent the most generalised level of 

This property is *optional* - base namespaces are an example of terms that do not have a namespace.

###### `lid` *Local Identifier*
The local identifier is a ***string*** code representing the *unique identifier* of the term *within its namespace*.

This property is *required*.

###### `gid` *Global Identifier*
The global identifier is a ***string*** value that uniquely identifies the term *among all namespaces*: it can be considered as the "*official*" term identifier.

By default the global identifier is composed by concatenating the *global identifier* of the *namespace term* to the *local identifier* of the *current term*, separated by a colon; if the namespace is missing, the local identifier becomes the global identifier. This is the default behaviour, in practice the value can be anything, provided it doesn't collide with another term's global identifier.

This property is *required*.

###### `_key` *Document key*

*[RDF]: Resource Description Framework
By default the *document key* of a term is a ***string*** obtained by hashing the global identifier. This is to allow for long identifiers, such as RDF identifiers, without impacting on the primary key; derived classes, however, may use different approaches.

#### Naming

Besides identifiers, that provide identification to computers, terms must be identifiable by humans, this set of properties is the default way of representing objects to users.

###### `name` *Name* or *Label*

This property represents the *name* or *label* assigned to the term, it is a short text that can be used as a label when displaying the term, or as a short description to be used in a list.

The property is implemented as an ***associative array*** in which *element keys* are *language codes* indicating the language in which the string is expressed in and the *value* is the actual string.

This structure should be used in all cases in which a string may be represented in different languages: for instance, categories or data types are concepts that must be made available in several languages, while scientific names are by definition in Latin. There will be gray areas in which the choice is up to users: the name of a person should not be translatable, but it may be transliterated, while city names are often translated in different languages.

###### `desc` *Definition* or *Description*

This property represents the full description of a term, it should provide all the necessary information, which could not be conveyed through the name, that can make the reader understand what the term expresses.

The structure of the property is the same as for `name`, except that in the former case the strings will be relatively short and can be indexed for searching purposes, while in the latter case strings will be a long text, best suited for a full text indexing engine.

#### Values

As with all objects in the library, no value can be `null` or empty: in that case the property should be omitted.

Besides the above *default* properties, a term can feature any other property, *provided it is defined in the data dictionary*.

#### Examples

A term representing a namespace:
```javascript
{
    _id:  "terms/14bc68dbb404f7061e9ce842578c58d0",
    lid:  "colours",
    gid:  "colours",
    name: {
        "en": "Colours namespace",
        "it": "Spazio dei nomi dei colori"
    },
    desc: {
        "en": "The namespace under which colours are defined",
        "it": "Spazio dei nomi a cui appartengono i colori"
    },
    ...
}
```

A term representing the red colour:
```javascript
{
    _id:  "terms/3446f3002f4087fb9de7f6b6c48b3e34",
    ns:   "terms/14bc68dbb404f7061e9ce842578c58d0"
    lid:  "red",
    gid:  "colours:red",
    name: {
        "en": "Red",
        "it": "Rosso"
    },
    desc: {
        "en": "The colour red",
        "it": "Il colore rosso"
    },
    ...
}
```
