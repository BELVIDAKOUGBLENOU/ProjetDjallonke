import 'package:isar/isar.dart';
import 'community.dart';

part 'premises.g.dart';

@Collection()
class Premise {
  Id id = Isar.autoIncrement;

  int? villageId;
  int? createdBy;
  int? communityId;
  String? code;
  String? address;
  String? gpsCoordinates;
  String? type;
  String? healthStatus;
  String? uid;
  int? version;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final community = IsarLink<Community>();
  final village = IsarLink<Village>();
  final creator = IsarLink<User>();
  final keepers = IsarLinks<PremisesKeeper>();
  final animals = IsarLinks<Animal>();
  final movementEventsFrom = IsarLinks<MovementEvent>();
  final movementEventsTo = IsarLinks<MovementEvent>();

  Premise({
    this.villageId,
    this.createdBy,
    this.communityId,
    this.code,
    this.address,
    this.gpsCoordinates,
    this.type,
    this.healthStatus,
    this.uid,
    this.version,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory Premise.fromJson(Map<String, dynamic> json) {
    return Premise(
      villageId: json['village_id'] as int?,
      createdBy: json['created_by'] as int?,
      communityId: json['community_id'] as int?,
      code: json['code'] as String?,
      address: json['address'] as String?,
      gpsCoordinates: json['gps_coordinates'] as String?,
      type: json['type'] as String?,
      healthStatus: json['health_status'] as String?,
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
      createdAt: json['created_at'] == null
          ? null
          : DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] == null
          ? null
          : DateTime.parse(json['updated_at'] as String),
      deletedAt: json['deleted_at'] == null
          ? null
          : DateTime.parse(json['deleted_at'] as String),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'village_id': villageId,
      'created_by': createdBy,
      'community_id': communityId,
      'code': code,
      'address': address,
      'gps_coordinates': gpsCoordinates,
      'type': type,
      'health_status': healthStatus,
      'uid': uid,
      'version': version,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'deleted_at': deletedAt?.toIso8601String(),
    };
  }

  Map<String, dynamic> toJsonWithRelations({bool includeRelations = false}) {
    final map = toJson();
    if (includeRelations && community.value != null) {
      map['community'] = community.value!.toJson();
    }
    return map;
  }

  static Future<List<Premise>> search(Isar isar, String? term) async {
    final q = term?.trim() ?? '';
    if (q.isEmpty) return [];

    return await isar.premises
        .filter()
        .codeContains(q, caseSensitive: false)
        .or()
        .addressContains(q, caseSensitive: false)
        .or()
        .typeContains(q, caseSensitive: false)
        .or()
        .healthStatusContains(q, caseSensitive: false)
        .findAll();
  }
}

// Related model stubs imports — adjust paths as needed in your project.
// Keep other stubs for related models — implement as needed elsewhere.
class Village {}

class User {}

class PremisesKeeper {}

class Animal {}

class MovementEvent {}

class Village {}

class User {}

class PremisesKeeper {}

class Animal {}

class MovementEvent {}
