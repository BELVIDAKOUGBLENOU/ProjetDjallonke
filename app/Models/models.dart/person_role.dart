import 'package:isar/isar.dart';

part 'person_role.g.dart';

@Collection()
class PersonRole {
  Id id = Isar.autoIncrement;

  int? personId;
  int? animalId;
  String? roleType;
  String? uid;
  int? version;

  final person = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();

  PersonRole(
      {this.personId, this.animalId, this.roleType, this.uid, this.version});

  factory PersonRole.fromJson(Map<String, dynamic> json) {
    return PersonRole(
      personId: json['person_id'] as int?,
      animalId: json['animal_id'] as int?,
      roleType: json['role_type'] as String?,
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'person_id': personId,
      'animal_id': animalId,
      'role_type': roleType,
      'uid': uid,
      'version': version,
    };
  }
}
