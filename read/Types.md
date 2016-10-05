# Types

> `Types` are `terms` that represent *data types*, *schemas*, *structures*, *categories* and *ontologies*.

A *term* becomes a *type* when its reference in an object's property is used to indicate a data or other type, or when it is used as a node in a directed graph.

#### Data types

Data types are terms used to indicate the kind of data a property may hold, it is used in *descriptors* to provide a way of validating data. Besides being used as the value of a property, data types are organised in a directed graph representing a structure where *specialised data types* are derived from *primitive data types*, each level provides information on how to test for valid data.

![image](ExampleTypes.svg)

A descriptor would have as its `:type:data` property a reference to one of the leaf nodes in the above diagram. Each node is a term abd represents a data type, the top node represents the primitive type, in this case a string, while the child nodes represent the specialised types.

In the above example, if the `:type:data:string:md5` term would be used as the data type, data would be validated top-down: the first operation would be to cast the value to a string, then the string would be set to lowercase and finally the string would be validated using the pattern in the leaf node, which accepts a 32 character alphanumeric string composed of the letters `a` through `f` and digits.

##### *Primitive default data types*
These data types represent the most generalised level of data types, it will generally represent the data types supported by the database.

The common namespace of all data types is `:type:data`.

- `:type:data:number` *Number*, this indicates a *numeric* (quantitative) value that can be in the form of an *integer* or *floating point number*. Using this type implies that the property can be both an integer or a float.
- `:type:data:string` *String*, this indicates a *text* value of undetermined size; by text we mean a string of `UTF8` characters, rather than a string of binary data.
- `:type:data:byte` *Bytes*, this indicates a string of *bytes* which may contain binary data not compatible with the `UTF8` character set.

##### *Derived default data types*
These data types represent a more specialised level of data types derived from the primitive data types.

- `:type:data:number`
  * `:type:data:number:int` *Signed integer*, this indicates a *signed integer* number of undefined format, that is, it may be of any valid size.
    * `:type:data:number:int:8` *8 bit signed integer*, it is an integer value that ranges by default from `-128` to `127`.
    * `:type:data:number:int:16` *16 bit signed integer*, it is an integer value that ranges by default from `-32768` to `32767`.
    * `:type:data:number:int:32` *32 bit signed integer*, it is an integer value that ranges by default from `-2^31^` to `2^31^-1`.
   * `:type:data:number:uint` *Unsigned integer*, this indicates an *unsigned integer* number of undefined format, that is, it may be of any valid size.