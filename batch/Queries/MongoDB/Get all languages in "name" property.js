db['ISO_3166-1'].aggregate(

	// Pipeline
	[
		// Stage 1
		{
			$project: {
			    // Aggregate all "name" property language keys in "languages" property.
			    "name": true,
			    "languages": {
			      $objectToArray: "$$ROOT.name"
			    }
			}
		},

		// Stage 2
		{
			$project: {
			    // Select only languages field.
			     "languages.k": true
			}
		},

		// Stage 3
		{
			$unwind: {
			      // Unwind all "languages" property keys.
			    path : "$languages"
			}
		},

		// Stage 4
		{
			$group: {
			      // Group language keys in "languages" property and remove "_id" to get one record.
			    "_id": null,
			    "languages": {
			      $addToSet: "$languages.k"
			    }
			}
		},

		// Stage 5
		{
			$project: {
			    // Remove "_id" property.
			    _id: false
			}
		},

	]

	// Created with Studio 3T, the IDE for MongoDB - https://studio3t.com/

);
