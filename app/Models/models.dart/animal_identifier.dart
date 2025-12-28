import 'package:isar/isar.dart';

part 'animal_identifier.g.dart';

@Collection()
class AnimalIdentifier {
  Id id = Isar.autoIncrement;

  int? animalId;
  String? type;
  String? code;
  bool? active;
  String? uid;
  int? version;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final animal = IsarLink<dynamic>();

  AnimalIdentifier(
      {this.animalId,
      this.type,
      this.code,
      this.active,
      this.uid,
      this.version});

  factory AnimalIdentifier.fromJson(Map<String, dynamic> json) {
    return AnimalIdentifier(
      animalId: json['animal_id'] as int?,
      type: json['type'] as String?,
      code: json['code'] as String?,
      active: json['active'] == null ? null : (json['active'] as bool),
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
      'animal_id': animalId,
      'type': type,
      'code': code,
      'active': active,
      'uid': uid,
      'version': version,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'deleted_at': deletedAt?.toIso8601String(),
    };
  }
}
