db['ISO_3166-2'].aggregate(

	// Pipeline
	[
		// Stage 1
		{
			$match: {
				// Select only those with types.
				"type": { "$exists": true }
			}
		},

		// Stage 2
		{
			$project: {
			    // Keep only "type" property.
			    "type": true
			}
		},

		// Stage 3
		{
			$group: {
			      // Group type in "types" property and remove "_id" to get one record.
			    "_id": null,
			    "types": {
			      $addToSet: "$type"
			    }
			}
		},

		// Stage 4
		{
			$project: {
			    // Remove "_id" property.
			    "_id": false
			}
		},

	]

	// Created with Studio 3T, the IDE for MongoDB - https://studio3t.com/

);
